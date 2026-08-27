<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class EmployeeSignatureStore
{
    public function storeDrawn(Employee $employee, ?string $dataUri): void
    {
        if (! is_string($dataUri) || ! str_starts_with($dataUri, 'data:image/png;base64,')) {
            return;
        }

        if (strlen($dataUri) < 2500) {
            return;
        }

        $binary = base64_decode(substr($dataUri, strpos($dataUri, ',') + 1), true);
        if ($binary === false || strlen($binary) < 200) {
            return;
        }

        $relative = 'employees/signatures/'.$employee->id.'-'.now()->format('YmdHis').'.png';
        Storage::disk('local')->put($relative, $binary);

        if (filled($employee->authorized_signature) && $employee->authorized_signature !== $relative) {
            Storage::disk('local')->delete($employee->authorized_signature);
        }

        $employee->forceFill(['authorized_signature' => $relative])->saveQuietly();
    }

    public function isolateInk(Employee $employee): string
    {
        if (blank($employee->authorized_signature)) {
            throw new RuntimeException('No hay una firma para procesar.');
        }

        $disk = Storage::disk('local');
        $source = $disk->path($employee->authorized_signature);

        if (! is_readable($source)) {
            throw new RuntimeException('No fue posible leer la firma.');
        }

        [$width, $height, $type] = getimagesize($source) ?: [0, 0, 0];
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };

        if (! $image || $width < 2 || $height < 2) {
            throw new RuntimeException('La firma debe ser JPG, PNG o WebP.');
        }

        $maxEdge = 1200;
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

        $this->knockOutPaper($working, $targetWidth, $targetHeight);
        $cropped = $this->cropToInk($working, $targetWidth, $targetHeight) ?? $working;
        if ($cropped !== $working) {
            imagedestroy($working);
        }

        $relative = 'employees/signatures/'.$employee->id.'-'.now()->format('YmdHis').'-ink.png';
        $destination = $disk->path($relative);
        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0775, true);
        }

        imagepng($cropped, $destination, 6);
        imagedestroy($cropped);

        if (filled($employee->authorized_signature) && $employee->authorized_signature !== $relative) {
            $disk->delete($employee->authorized_signature);
        }

        $employee->forceFill(['authorized_signature' => $relative])->saveQuietly();

        return $relative;
    }

    private function knockOutPaper(\GdImage $image, int $width, int $height): void
    {
        $paper = $this->samplePaper($image, $width, $height);
        $paperLuma = $this->luma($paper[0], $paper[1], $paper[2]);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);
                $alpha = ($rgba >> 24) & 0x7F;
                if ($alpha >= 120) {
                    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 0, 0, 0, 127));

                    continue;
                }

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $luma = $this->luma($r, $g, $b);
                $distance = hypot($r - $paper[0], hypot($g - $paper[1], $b - $paper[2]));
                $darkness = $paperLuma - $luma;
                $ink = max($distance / 90, $darkness / 70);

                if ($ink < 0.22) {
                    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 0, 0, 0, 127));

                    continue;
                }

                $gdAlpha = (int) round((1 - min(1, $ink)) * 127);
                $gdAlpha = max(0, min(110, $gdAlpha));
                imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, $gdAlpha));
            }
        }
    }

    /** @return array{int, int, int} */
    private function samplePaper(\GdImage $image, int $width, int $height): array
    {
        $points = [
            [0, 0], [$width - 1, 0], [0, $height - 1], [$width - 1, $height - 1],
            [(int) ($width * .5), 0], [(int) ($width * .5), $height - 1],
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
            return [255, 255, 255];
        }

        return [
            (int) round($sum[0] / $count),
            (int) round($sum[1] / $count),
            (int) round($sum[2] / $count),
        ];
    }

    private function cropToInk(\GdImage $image, int $width, int $height): ?\GdImage
    {
        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (((imagecolorat($image, $x, $y) >> 24) & 0x7F) >= 110) {
                    continue;
                }

                $minX = min($minX, $x);
                $minY = min($minY, $y);
                $maxX = max($maxX, $x);
                $maxY = max($maxY, $y);
            }
        }

        if ($maxX < $minX || $maxY < $minY) {
            return null;
        }

        $padX = max(4, (int) round(($maxX - $minX + 1) * .08));
        $padY = max(4, (int) round(($maxY - $minY + 1) * .12));
        $left = max(0, $minX - $padX);
        $top = max(0, $minY - $padY);
        $cropWidth = min($width - $left, $maxX - $minX + 1 + ($padX * 2));
        $cropHeight = min($height - $top, $maxY - $minY + 1 + ($padY * 2));

        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        $clear = imagecolorallocatealpha($cropped, 0, 0, 0, 127);
        imagefilledrectangle($cropped, 0, 0, $cropWidth - 1, $cropHeight - 1, $clear);
        imagecopy($cropped, $image, 0, 0, $left, $top, $cropWidth, $cropHeight);

        return $cropped;
    }

    private function luma(int $r, int $g, int $b): float
    {
        return (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
    }
}
