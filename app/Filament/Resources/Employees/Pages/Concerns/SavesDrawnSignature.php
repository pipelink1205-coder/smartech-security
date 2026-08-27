<?php

namespace App\Filament\Resources\Employees\Pages\Concerns;

use App\Services\Employees\EmployeeSignatureStore;

trait SavesDrawnSignature
{
    protected ?string $drawnSignature = null;

    protected function persistDrawnSignature(array $data): array
    {
        $drawn = $data['signature_drawn'] ?? null;
        unset($data['signature_drawn']);

        $this->drawnSignature = is_string($drawn) ? $drawn : null;

        return $data;
    }

    protected function storeDrawnSignature(): void
    {
        if (blank($this->drawnSignature ?? null)) {
            return;
        }

        app(EmployeeSignatureStore::class)->storeDrawn($this->record, $this->drawnSignature);
        $this->drawnSignature = null;
    }
}
