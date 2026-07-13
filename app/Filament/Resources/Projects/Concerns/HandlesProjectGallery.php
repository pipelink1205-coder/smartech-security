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
            ->orderBy('sort_order')
            ->orderByDesc('is_cover')
            ->pluck('path')
            ->all();

        return $data;
    }

    /**
     * Reconcilia la galería del formulario con las filas de ProjectImage.
     * Las fotos nuevas que FilePond antepone se mueven al final para no romper la portada.
     */
    protected function syncGallery(): void
    {
        $paths = array_values(array_filter(
            Arr::wrap($this->data['gallery'] ?? []),
            static fn ($path) => filled($path),
        ));

        $project = $this->record;
        $existing = $project->images()->get()->keyBy('path');
        $existingPaths = $existing->keys()->all();
        $existingLookup = array_flip($existingPaths);

        // Si FilePond metió fotos nuevas al inicio, pásalas al final.
        $leadingNew = [];
        while ($paths !== [] && ! isset($existingLookup[$paths[0]])) {
            $leadingNew[] = array_shift($paths);
        }
        $paths = array_values(array_merge($paths, $leadingNew));

        $keepIds = [];

        foreach ($paths as $index => $path) {
            $row = $existing->get($path);

            if ($row) {
                $row->update(['sort_order' => $index, 'is_cover' => $index === 0]);
                $keepIds[] = $row->id;
            } else {
                $keepIds[] = $project->images()->create([
                    'path' => $path,
                    'sort_order' => $index,
                    'is_cover' => $index === 0,
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
