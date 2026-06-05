<?php

namespace App\Filament\Resources\Projects\Concerns;

use Illuminate\Support\Arr;

trait HandlesProjectGallery
{
    protected function stripVirtualProjectFields(array $data): array
    {
        unset($data['pending_gallery']);

        return $data;
    }

    protected function syncPendingGallery(): void
    {
        $paths = Arr::wrap($this->data['pending_gallery'] ?? []);

        if ($paths === []) {
            return;
        }

        $sortOrder = (int) ($this->record->images()->max('sort_order') ?? 0);

        foreach (array_values($paths) as $path) {
            if (blank($path)) {
                continue;
            }

            $sortOrder++;

            $this->record->images()->create([
                'path'       => $path,
                'sort_order' => $sortOrder,
            ]);
        }

        $this->data['pending_gallery'] = [];
    }
}
