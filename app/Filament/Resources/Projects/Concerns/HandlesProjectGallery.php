<?php

namespace App\Filament\Resources\Projects\Concerns;

use Illuminate\Support\Arr;

trait HandlesProjectGallery
{
    protected function stripVirtualProjectFields(array $data): array
    {
        unset($data['gallery']);

        return $data;
    }

    /**
     * Precarga la galería (rutas ordenadas) en el formulario al editar.
     */
    protected function fillGalleryState(array $data): array
    {
        $data['gallery'] = $this->record
            ->images()
            ->orderByDesc('is_cover')
            ->orderBy('sort_order')
            ->pluck('path')
            ->all();

        return $data;
    }

    /**
     * Reconcilia la galería del formulario con las filas de ProjectImage:
     * conserva el orden, marca la primera como portada y elimina las quitadas.
     * (No borra archivos físicos por si están referenciados en otro proyecto.)
     */
    protected function syncGallery(): void
    {
        $paths = array_values(array_filter(
            Arr::wrap($this->data['gallery'] ?? []),
            static fn ($path) => filled($path),
        ));

        $project = $this->record;
        $existing = $project->images()->get()->keyBy('path');
        $keepIds = [];

        foreach ($paths as $index => $path) {
            $row = $existing->get($path);

            if ($row) {
                $row->update(['sort_order' => $index, 'is_cover' => $index === 0]);
                $keepIds[] = $row->id;
            } else {
                $keepIds[] = $project->images()->create([
                    'path'       => $path,
                    'sort_order' => $index,
                    'is_cover'   => $index === 0,
                ])->id;
            }
        }

        $project->images()
            ->whereNotIn('id', $keepIds ?: [0])
            ->delete();

        if (! $project->images()->where('is_cover', true)->exists()) {
            $project->images()->orderBy('sort_order')->first()?->update(['is_cover' => true]);
        }
    }
}
