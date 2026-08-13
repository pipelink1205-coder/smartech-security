<?php

namespace App\Services\Dian;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Genera el código QR exigido en la representación gráfica de la
 * factura electrónica DIAN (endroid/qr-code v6).
 */
class QrGenerator
{
    /**
     * Devuelve un Data URI (base64) listo para incrustar en un <img src=".."> del PDF.
     */
    public function dataUri(string $qrUrl, int $size = 220): string
    {
        $builder = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,
            data: $qrUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 5,
        );

        return $builder->build()->getDataUri();
    }
}
