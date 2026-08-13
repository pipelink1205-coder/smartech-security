<?php

namespace App\Domain\Quotes;

use App\Models\QuoteCatalogItem;
use App\Models\QuoteItem;
use Illuminate\Support\Str;

final class CatalogItemSync
{
    /**
     * Si la línea no viene del catálogo, crea o reutiliza un concepto comercial
     * y deja el ítem vinculado para próximas cotizaciones.
     */
    public static function ensureForQuoteItem(QuoteItem $item): ?QuoteCatalogItem
    {
        if (filled($item->quote_catalog_item_id) || blank($item->concept)) {
            return $item->catalogItem;
        }

        $name = trim((string) $item->concept);
        $type = filled($item->type) ? (string) $item->type : 'product';

        $existing = QuoteCatalogItem::query()
            ->where('type', $type)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if ($existing) {
            return $existing;
        }

        $code = filled($item->code)
            ? Str::upper(trim((string) $item->code))
            : self::uniqueCodeFromName($name);

        $byCode = QuoteCatalogItem::query()->where('code', $code)->first();
        if ($byCode) {
            return $byCode;
        }

        return QuoteCatalogItem::create([
            'type' => $type,
            'code' => $code,
            'name' => $name,
            'description' => filled($item->description) ? (string) $item->description : $name,
            'unit' => filled($item->unit) ? (string) $item->unit : 'unidad',
            'default_unit_price' => (float) ($item->unit_price ?? 0),
            'default_tax_rate' => (float) ($item->tax_rate ?? 0),
            'is_active' => true,
        ]);
    }

    private static function uniqueCodeFromName(string $name): string
    {
        $base = Str::upper(Str::slug($name, '_'));
        $base = substr($base !== '' ? $base : 'ITEM', 0, 40);
        $code = $base;
        $i = 2;

        while (QuoteCatalogItem::query()->where('code', $code)->exists()) {
            $suffix = '_'.$i;
            $code = substr($base, 0, max(1, 40 - strlen($suffix))).$suffix;
            $i++;
        }

        return $code;
    }
}
