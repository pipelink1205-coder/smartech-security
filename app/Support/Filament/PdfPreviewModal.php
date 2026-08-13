<?php

namespace App\Support\Filament;

use Illuminate\Support\HtmlString;

final class PdfPreviewModal
{
    public static function content(string $url, string $title = 'Vista previa PDF'): HtmlString
    {
        return new HtmlString(view('filament.pdf-preview', [
            'url' => $url,
            'title' => $title,
        ])->render());
    }
}
