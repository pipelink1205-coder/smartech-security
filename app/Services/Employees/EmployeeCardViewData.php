<?php

namespace App\Services\Employees;

use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class EmployeeCardViewData
{
    /** @return array<string, mixed> */
    public function forEmployee(Employee $employee): array
    {
        return $this->make([
            'first_names' => $employee->first_names,
            'last_names' => $employee->last_names,
            'position' => $employee->position,
            'employee_code' => $employee->employee_code,
            'portrait_scale' => $employee->portrait_scale,
            'portrait_x' => $employee->portrait_x,
            'portrait_y' => $employee->portrait_y,
            'portrait' => $employee->portrait_data_uri,
            'signature' => $employee->card_signature_data_uri,
            'qr' => app(EmployeeVerificationQr::class)->dataUri($employee),
        ]);
    }

    /** @param array<string, mixed> $state
     * @return array<string, mixed>
     */
    public function forForm(array $state, ?Employee $employee = null): array
    {
        $cardPhotoState = $state['photo_card'] ?? null;
        $portrait = $this->hasTemporaryUpload($cardPhotoState)
            ? $this->stateImageDataUri($cardPhotoState)
            : ($this->stateImageDataUri($state['photo_cutout'] ?? null)
                ?? $this->stateImageDataUri($cardPhotoState)
                ?? $employee?->portrait_data_uri);

        $ownSignature = $this->stateImageDataUri($state['authorized_signature'] ?? null)
            ?? $this->drawnSignatureDataUri($state['signature_drawn'] ?? null)
            ?? $employee?->authorized_signature_data_uri;
        $isRep = (bool) ($state['is_legal_representative'] ?? $employee?->is_legal_representative);
        $legalRep = Employee::legalRepresentative();
        if ($legalRep && $employee && $legalRep->is($employee) && ! $isRep) {
            $legalRep = null;
        }
        $signature = $isRep
            ? $ownSignature
            : $legalRep?->authorized_signature_data_uri;

        return $this->make([
            'first_names' => $state['first_names'] ?? $employee?->first_names,
            'last_names' => $state['last_names'] ?? $employee?->last_names,
            'position' => $state['position'] ?? $employee?->position,
            'employee_code' => $employee?->employee_code,
            'portrait_scale' => $state['portrait_scale'] ?? $employee?->portrait_scale,
            'portrait_x' => $state['portrait_x'] ?? $employee?->portrait_x,
            'portrait_y' => $state['portrait_y'] ?? $employee?->portrait_y,
            'portrait' => $portrait,
            'signature' => $signature,
            'qr' => $employee ? app(EmployeeVerificationQr::class)->dataUri($employee) : null,
        ]);
    }

    /** @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function make(array $data): array
    {
        return [
            'front_template' => $this->publicImageDataUri('designs/papeleria/plantilla-carnet-frente-v1.png'),
            'front_foreground' => $this->publicImageDataUri('designs/papeleria/plantilla-carnet-frente-capa-superior-v1.png'),
            'back_template' => $this->publicImageDataUri('designs/papeleria/plantilla-carnet-reverso-v1.png'),
            'full_name' => mb_strtoupper(trim(($data['first_names'] ?? '').' '.($data['last_names'] ?? ''))) ?: 'NOMBRE DEL EMPLEADO',
            'position' => trim((string) ($data['position'] ?? '')) ?: 'Cargo del empleado',
            'employee_code' => $data['employee_code'] ?: 'STS-XXXX',
            'portrait_scale' => max(50, min(150, (int) ($data['portrait_scale'] ?: 88))),
            'portrait_x' => max(-40, min(40, (int) ($data['portrait_x'] ?? 4))),
            'portrait_y' => max(-40, min(40, (int) ($data['portrait_y'] ?? 2))),
            'portrait' => $data['portrait'] ?? null,
            'signature' => $data['signature'] ?? null,
            'qr' => $data['qr'] ?? null,
        ];
    }

    private function publicImageDataUri(string $relativePath): string
    {
        $path = public_path($relativePath);
        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }

    private function stateImageDataUri(mixed $state): ?string
    {
        if (is_array($state)) {
            $state = collect($state)->first(fn (mixed $value): bool => filled($value));
        }

        if ($state instanceof TemporaryUploadedFile) {
            try {
                return $state->temporaryUrl();
            } catch (Throwable) {
                return null;
            }
        }

        if (! is_string($state) || blank($state) || ! Storage::disk('local')->exists($state)) {
            return null;
        }

        $mime = Storage::disk('local')->mimeType($state) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('local')->get($state));
    }

    private function drawnSignatureDataUri(mixed $state): ?string
    {
        return is_string($state) && str_starts_with($state, 'data:image/png;base64,') && strlen($state) > 2500
            ? $state
            : null;
    }

    private function hasTemporaryUpload(mixed $state): bool
    {
        if (is_array($state)) {
            return collect($state)->contains(fn (mixed $value): bool => $value instanceof TemporaryUploadedFile);
        }

        return $state instanceof TemporaryUploadedFile;
    }
}
