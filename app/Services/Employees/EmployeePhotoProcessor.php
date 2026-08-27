<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use SplQueue;

class EmployeePhotoProcessor
{
    public function process(Employee $employee): string
    {
        if (blank($employee->photo_card)) {
            throw new RuntimeException('Carga primero la fotografía para el carnet.');
        }

        $disk = Storage::disk('local');
        $source = $disk->path($employee->photo_card);

        if (! is_readable($source)) {
            throw new RuntimeException('No fue posible leer la fotografía para el carnet.');
        }

        [$width, $height, $type] = getimagesize($source) ?: [0, 0, 0];
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };

        if (! $image || $width < 2 || $height < 2) {
            throw new RuntimeException('La fotografía debe ser JPG, PNG o WebP.');
        }

        $maxEdge = 1100;
        $ratio = min(1, $maxEdge / max($width, $height));
        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $working = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($working, false);
        imagesavealpha($working, true);
        $clear = imagecolorallocatealpha($working, 0, 0, 0, 127);
        imagefilledrectangle($working, 0, 0, $targetWidth - 1, $targetHeight - 1, $clear);
        imagecopyresampled($working, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($image);

        $alreadyCutout = $this->hasUsefulAlpha($working, $targetWidth, $targetHeight);
        $this->removeCheckerboard($working, $targetWidth, $targetHeight);

        if (! $alreadyCutout) {
            $background = $this->sampleBackground($working, $targetWidth, $targetHeight);
            if ($background !== null) {
                $this->removeConnectedBackground($working, $targetWidth, $targetHeight, $background);
            }
        }

        $relative = 'employees/cutouts/'.$employee->id.'-'.now()->format('YmdHis').'.png';
        $destination = $disk->path($relative);
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0775, true);
        }

        imagepng($working, $destination, 6);
        imagedestroy($working);

        if (filled($employee->photo_cutout) && $employee->photo_cutout !== $relative) {
            $disk->delete($employee->photo_cutout);
        }

        $employee->forceFill(['photo_cutout' => $relative])->saveQuietly();

        return $relative;
    }

    private function hasUsefulAlpha(\GdImage $image, int $width, int $height): bool
    {
        $transparent = 0;
        $samples = 0;
        $stepX = max(1, (int) floor($width / 40));
        $stepY = max(1, (int) floor($height / 40));

        for ($y = 0; $y < $height; $y += $stepY) {
            for ($x = 0; $x < $width; $x += $stepX) {
                $samples++;
                if (((imagecolorat($image, $x, $y) >> 24) & 0x7F) >= 16) {
                    $transparent++;
                }
            }
        }

        return $samples > 0 && ($transparent / $samples) >= 0.08;
    }

    /**
     * Quita el tablero gris/blanco que dejan Photoshop, Photopea o capturas
     * de “PNG transparente” que en realidad no tienen canal alfa.
     */
    private function removeCheckerboard(\GdImage $image, int $width, int $height): void
    {
        $removed = 0;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                if ($alpha >= 120) {
                    continue;
                }

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if (! $this->isCheckerboardSwatch($r, $g, $b)) {
                    continue;
                }

                imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, 127));
                $removed++;
            }
        }

        if ($removed === 0) {
            return;
        }
    }

    private function isCheckerboardSwatch(int $r, int $g, int $b): bool
    {
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $chroma = $max - $min;

        if ($chroma > 14) {
            return false;
        }

        $pairs = [
            [255, 255, 255],
            [240, 240, 240],
            [227, 227, 227],
            [204, 204, 204],
            [192, 192, 192],
            [153, 153, 153],
        ];

        foreach ($pairs as [$pr, $pg, $pb]) {
            if (abs($r - $pr) <= 10 && abs($g - $pg) <= 10 && abs($b - $pb) <= 10) {
                return true;
            }
        }

        return false;
    }

    /** @return array{int, int, int}|null */
    private function sampleBackground(\GdImage $image, int $width, int $height): ?array
    {
        $points = [
            [0, 0], [$width - 1, 0], [(int) ($width * .5), 0],
            [0, (int) ($height * .2)], [$width - 1, (int) ($height * .2)],
        ];
        $sum = [0, 0, 0];
        $count = 0;

        foreach ($points as [$x, $y]) {
            $rgb = imagecolorat($image, $x, $y);
            if ((($rgb >> 24) & 0x7F) >= 80) {
                continue;
            }
            $sum[0] += ($rgb >> 16) & 0xFF;
            $sum[1] += ($rgb >> 8) & 0xFF;
            $sum[2] += $rgb & 0xFF;
            $count++;
        }

        if ($count === 0) {
            return null;
        }

        $sample = array_map(fn (int $value): int => (int) round($value / $count), $sum);

        // Un PNG ya recortado deja las esquinas negras transparentes; no las uses como fondo.
        if ($sample[0] < 18 && $sample[1] < 18 && $sample[2] < 18) {
            return null;
        }

        return $sample;
    }

    /** @param array{int, int, int} $background */
    private function removeConnectedBackground(\GdImage $image, int $width, int $height, array $background): void
    {
        $softStart = 20.0;
        $softEnd = 78.0;
        $visited = str_repeat("\0", $width * $height);
        $queue = new SplQueue;

        for ($x = 0; $x < $width; $x++) {
            $queue->enqueue($x);
            $queue->enqueue((($height - 1) * $width) + $x);
        }
        for ($y = 1; $y < $height - 1; $y++) {
            $queue->enqueue($y * $width);
            $queue->enqueue(($y * $width) + $width - 1);
        }

        while (! $queue->isEmpty()) {
            $index = $queue->dequeue();
            if ($visited[$index] !== "\0") {
                continue;
            }
            $visited[$index] = "\1";
            $x = $index % $width;
            $y = intdiv($index, $width);
            $rgba = imagecolorat($image, $x, $y);
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;
            $distance = sqrt(
                (($r - $background[0]) ** 2) +
                (($g - $background[1]) ** 2) +
                (($b - $background[2]) ** 2)
            );

            if ($distance > $softEnd) {
                continue;
            }

            $alpha = $distance <= $softStart
                ? 127
                : (int) round(127 * (1 - (($distance - $softStart) / ($softEnd - $softStart))));
            imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $alpha));

            if ($x > 0) {
                $queue->enqueue($index - 1);
            }
            if ($x + 1 < $width) {
                $queue->enqueue($index + 1);
            }
            if ($y > 0) {
                $queue->enqueue($index - $width);
            }
            if ($y + 1 < $height) {
                $queue->enqueue($index + $width);
            }
        }
    }
}
