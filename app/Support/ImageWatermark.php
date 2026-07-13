<?php

namespace App\Support;

/**
 * Marca de agua tenue (escudo centrado) sobre fotos de proyectos/servicios.
 * Usa GD; elimina el fondo blanco del logo y deja solo la silueta del escudo.
 */
final class ImageWatermark
{
    public static function apply(string $imagePath, ?string $logoPath = null, float $opacity = 0.22): bool
    {
        if (! is_readable($imagePath) || ! extension_loaded('gd')) {
            return false;
        }

        $logoPath ??= public_path('images/logo.png');
        if (! is_readable($logoPath)) {
            return false;
        }

        $image = self::createImage($imagePath, applyExifOrientation: true);
        $logo = self::createImage($logoPath, applyExifOrientation: false);

        if ($image === null || $logo === null) {
            return false;
        }

        // Quitar el marco/fondo blanco: solo queda el escudo verde + águila.
        $logo = self::knockoutWhiteBackground($logo);

        $imgW = imagesx($image);
        $imgH = imagesy($image);
        $logoW = imagesx($logo);
        $logoH = imagesy($logo);

        if ($imgW < 80 || $imgH < 80 || $logoW < 1 || $logoH < 1) {
            imagedestroy($image);
            imagedestroy($logo);

            return false;
        }

        // Escudo ~28% del lado menor, centrado.
        $target = (int) max(48, min($imgW, $imgH) * 0.28);
        $scale = $target / max($logoW, $logoH);
        $dstW = max(1, (int) round($logoW * $scale));
        $dstH = max(1, (int) round($logoH * $scale));

        $resized = imagecreatetruecolor($dstW, $dstH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $dstW, $dstH, $transparent);
        imagecopyresampled($resized, $logo, 0, 0, 0, 0, $dstW, $dstH, $logoW, $logoH);
        self::keepShieldSilhouetteOnly($resized);

        $x = (int) round(($imgW - $dstW) / 2);
        $y = (int) round(($imgH - $dstH) / 2);

        imagealphablending($image, true);
        self::mergeWithOpacity($image, $resized, $x, $y, $opacity);

        $ok = self::saveImage($image, $imagePath);

        imagedestroy($resized);
        imagedestroy($logo);
        imagedestroy($image);

        return $ok;
    }

    /**
     * Flood-fill desde los bordes: el blanco externo pasa a transparente.
     * El águila blanca dentro del escudo se conserva.
     */
    private static function knockoutWhiteBackground(\GdImage $logo): \GdImage
    {
        $w = imagesx($logo);
        $h = imagesy($logo);

        imagealphablending($logo, false);
        imagesavealpha($logo, true);

        $visited = array_fill(0, $w * $h, false);
        $queue = [];

        $push = static function (int $x, int $y) use (&$queue, &$visited, $w, $h): void {
            if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) {
                return;
            }
            $i = $y * $w + $x;
            if ($visited[$i]) {
                return;
            }
            $visited[$i] = true;
            $queue[] = [$x, $y];
        };

        for ($x = 0; $x < $w; $x++) {
            $push($x, 0);
            $push($x, $h - 1);
        }
        for ($y = 0; $y < $h; $y++) {
            $push(0, $y);
            $push($w - 1, $y);
        }

        $transparent = imagecolorallocatealpha($logo, 0, 0, 0, 127);

        while ($queue !== []) {
            [$x, $y] = array_pop($queue);
            $rgba = imagecolorat($logo, $x, $y);
            $a = ($rgba & 0x7F000000) >> 24;
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;

            // Ya transparente o no es “fondo/halo” claro
            if ($a >= 120 || ! self::isKnockoutCandidate($r, $g, $b)) {
                continue;
            }

            imagesetpixel($logo, $x, $y, $transparent);

            $push($x + 1, $y);
            $push($x - 1, $y);
            $push($x, $y + 1);
            $push($x, $y - 1);
        }

        // Segunda pasada: halo anti-alias claro pegado a zona ya transparente.
        self::clearLightFringe($logo);

        return $logo;
    }

    private static function isKnockoutCandidate(int $r, int $g, int $b): bool
    {
        $min = min($r, $g, $b);
        $max = max($r, $g, $b);
        $luma = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

        // Blanco / casi blanco / gris claro del marco.
        if ($min >= 210 && ($max - $min) <= 28) {
            return true;
        }

        // Halo anti-alias muy claro (no es el verde del escudo).
        if ($luma >= 200 && ($max - $min) <= 40 && $g < 200) {
            return true;
        }

        return false;
    }

    /**
     * Conserva solo el verde del escudo y el blanco del águila;
     * elimina halos / marco claro residual.
     */
    private static function keepShieldSilhouetteOnly(\GdImage $img): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ($rgba & 0x7F000000) >> 24;
                if ($a >= 120) {
                    continue;
                }

                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;

                if (! self::isShieldOrEaglePixel($r, $g, $b)) {
                    imagesetpixel($img, $x, $y, $transparent);
                }
            }
        }
    }

    private static function isShieldOrEaglePixel(int $r, int $g, int $b): bool
    {
        // Cuerpo verde/teal del escudo (marca).
        if ($g >= 85 && $g >= $r + 8 && $g >= $b - 8) {
            return true;
        }

        // Cabeza del águila (blanco interior, ya sin el marco externo).
        $min = min($r, $g, $b);
        $max = max($r, $g, $b);

        return $min >= 215 && ($max - $min) <= 25;
    }

    private static function clearLightFringe(\GdImage $logo): void
    {
        $w = imagesx($logo);
        $h = imagesy($logo);
        $transparent = imagecolorallocatealpha($logo, 0, 0, 0, 127);

        for ($pass = 0; $pass < 2; $pass++) {
            $toClear = [];

            for ($y = 0; $y < $h; $y++) {
                for ($x = 0; $x < $w; $x++) {
                    $rgba = imagecolorat($logo, $x, $y);
                    $a = ($rgba & 0x7F000000) >> 24;
                    if ($a >= 120) {
                        continue;
                    }

                    $r = ($rgba >> 16) & 0xFF;
                    $g = ($rgba >> 8) & 0xFF;
                    $b = $rgba & 0xFF;
                    $luma = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);

                    // Solo franja clara (no el verde del escudo).
                    if ($luma < 175 || $g > ($r + 25)) {
                        continue;
                    }

                    $nearTransparent = false;
                    foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as [$dx, $dy]) {
                        $nx = $x + $dx;
                        $ny = $y + $dy;
                        if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) {
                            $nearTransparent = true;
                            break;
                        }
                        $na = (imagecolorat($logo, $nx, $ny) & 0x7F000000) >> 24;
                        if ($na >= 120) {
                            $nearTransparent = true;
                            break;
                        }
                    }

                    if ($nearTransparent) {
                        $toClear[] = [$x, $y];
                    }
                }
            }

            foreach ($toClear as [$x, $y]) {
                imagesetpixel($logo, $x, $y, $transparent);
            }
        }
    }

    private static function isBackgroundWhite(int $r, int $g, int $b): bool
    {
        return self::isKnockoutCandidate($r, $g, $b);
    }

    private static function createImage(string $path, bool $applyExifOrientation = false): ?\GdImage
    {
        $info = @getimagesize($path);
        if ($info === false) {
            return null;
        }

        $image = match ($info[2] ?? null) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path) ?: null,
            IMAGETYPE_PNG => @imagecreatefrompng($path) ?: null,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            IMAGETYPE_GIF => @imagecreatefromgif($path) ?: null,
            default => null,
        };

        if ($image === null) {
            return null;
        }

        // GD ignora Orientation: si no la aplicamos antes de guardar, el JPEG
        // pierde el tag EXIF y fotos de celular se ven giradas 90°.
        if ($applyExifOrientation && ($info[2] ?? null) === IMAGETYPE_JPEG) {
            $image = self::applyExifOrientation($image, $path);
        }

        return $image;
    }

    /**
     * Corrige la orientación de píxeles según EXIF (Orientation 1–8).
     * imagerotate() en GD gira en sentido antihorario.
     */
    private static function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if ($orientation <= 1) {
            return $image;
        }

        $rotated = match ($orientation) {
            2 => self::flipImage($image, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($image, 180, 0) ?: $image,
            4 => self::flipImage($image, IMG_FLIP_VERTICAL),
            5 => self::flipImage(imagerotate($image, 270, 0) ?: $image, IMG_FLIP_HORIZONTAL),
            6 => imagerotate($image, 270, 0) ?: $image, // 90° CW
            7 => self::flipImage(imagerotate($image, 90, 0) ?: $image, IMG_FLIP_HORIZONTAL),
            8 => imagerotate($image, 90, 0) ?: $image, // 270° CW
            default => $image,
        };

        if ($rotated !== $image) {
            imagedestroy($image);
        }

        return $rotated;
    }

    private static function flipImage(\GdImage $image, int $mode): \GdImage
    {
        if (function_exists('imageflip')) {
            imageflip($image, $mode);

            return $image;
        }

        return $image;
    }

    private static function saveImage(\GdImage $image, string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'jpg', 'jpeg' => imagejpeg($image, $path, 90),
            'png' => imagepng($image, $path, 6),
            'webp' => function_exists('imagewebp') && imagewebp($image, $path, 90),
            'gif' => imagegif($image, $path),
            default => false,
        };
    }

    private static function mergeWithOpacity(\GdImage $dst, \GdImage $src, int $x, int $y, float $opacity): void
    {
        $opacity = max(0.0, min(1.0, $opacity));
        $w = imagesx($src);
        $h = imagesy($src);

        for ($sy = 0; $sy < $h; $sy++) {
            for ($sx = 0; $sx < $w; $sx++) {
                $rgba = imagecolorat($src, $sx, $sy);
                $sa = ($rgba & 0x7F000000) >> 24;
                $sr = ($rgba >> 16) & 0xFF;
                $sg = ($rgba >> 8) & 0xFF;
                $sb = $rgba & 0xFF;

                // GD alpha: 0 = opaco, 127 = transparente
                $srcAlpha = (127 - $sa) / 127;
                $a = $srcAlpha * $opacity;
                if ($a <= 0.01) {
                    continue;
                }

                $dx = $x + $sx;
                $dy = $y + $sy;
                if ($dx < 0 || $dy < 0 || $dx >= imagesx($dst) || $dy >= imagesy($dst)) {
                    continue;
                }

                $drgba = imagecolorat($dst, $dx, $dy);
                $dr = ($drgba >> 16) & 0xFF;
                $dg = ($drgba >> 8) & 0xFF;
                $db = $drgba & 0xFF;

                $nr = (int) round($sr * $a + $dr * (1 - $a));
                $ng = (int) round($sg * $a + $dg * (1 - $a));
                $nb = (int) round($sb * $a + $db * (1 - $a));

                imagesetpixel($dst, $dx, $dy, imagecolorallocate($dst, $nr, $ng, $nb));
            }
        }
    }
}
