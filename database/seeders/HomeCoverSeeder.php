<?php

namespace Database\Seeders;

use App\Models\HomeCover;
use Illuminate\Database\Seeder;

class HomeCoverSeeder extends Seeder
{
    public function run(): void
    {
        $cover = HomeCover::fromSiteConfig();

        HomeCover::query()->firstOrCreate(
            ['slug' => 'institucional'],
            [
                'name' => $cover->name,
                'kind' => HomeCover::KIND_INSTITUCIONAL,
                'badge' => $cover->badge,
                'title' => $cover->title,
                'subtitle' => $cover->subtitle,
                'image' => $cover->image,
                'image_alt' => $cover->image_alt,
                'cta_primary_label' => $cover->cta_primary_label,
                'cta_primary_url' => $cover->cta_primary_url,
                'cta_secondary_label' => $cover->cta_secondary_label,
                'cta_secondary_url' => $cover->cta_secondary_url,
                'strip_label' => $cover->strip_label,
                'strip_highlights' => $cover->strip_highlights,
                'stats' => $cover->stats,
                'is_published' => true,
                'is_fallback' => true,
                'priority' => 0,
                'starts_at' => null,
                'ends_at' => null,
            ]
        );
    }
}
