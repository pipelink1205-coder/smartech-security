<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Employee extends Model
{
    public const STATUSES = [
        'active' => 'Activo',
        'suspended' => 'Suspendido',
        'retired' => 'Retirado',
    ];

    protected $fillable = [
        'employee_code', 'first_names', 'last_names', 'document_type', 'document_number',
        'position', 'area', 'email', 'phone', 'photo_original', 'photo_card', 'photo_cutout',
        'authorized_signature', 'portrait_scale', 'portrait_x', 'portrait_y', 'status',
        'is_legal_representative', 'started_at', 'notes', 'verification_token',
    ];

    protected $hidden = [
        'document_number',
        'verification_token',
    ];

    protected $casts = [
        'document_number' => 'encrypted',
        'started_at' => 'date',
        'portrait_scale' => 'integer',
        'portrait_x' => 'integer',
        'portrait_y' => 'integer',
        'is_legal_representative' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee): void {
            if (blank($employee->verification_token)) {
                $employee->verification_token = Str::random(48);
            }
        });

        static::created(function (Employee $employee): void {
            if (blank($employee->employee_code)) {
                $employee->forceFill([
                    'employee_code' => 'STS-'.str_pad((string) $employee->getKey(), 4, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });

        static::saved(function (Employee $employee): void {
            if (! $employee->is_legal_representative) {
                return;
            }

            static::query()
                ->whereKeyNot($employee->id)
                ->where('is_legal_representative', true)
                ->update(['is_legal_representative' => false]);
        });

        static::deleting(function (Employee $employee): void {
            Storage::disk('local')->delete(array_filter([
                $employee->photo_original,
                $employee->photo_card,
                $employee->photo_cutout,
                $employee->authorized_signature,
            ]));
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_names.' '.$this->last_names);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public static function legalRepresentative(): ?self
    {
        return static::query()
            ->where('is_legal_representative', true)
            ->orderByDesc('id')
            ->first();
    }

    public function getPortraitDataUriAttribute(): ?string
    {
        $path = filled($this->photo_cutout) ? $this->photo_cutout : $this->photo_card;

        return $this->privateImageDataUri($path);
    }

    public function getPublicPhotoDataUriAttribute(): ?string
    {
        return $this->privateImageDataUri($this->photo_original)
            ?? $this->privateImageDataUri($this->photo_card)
            ?? $this->portrait_data_uri;
    }

    public function getAuthorizedSignatureDataUriAttribute(): ?string
    {
        return $this->privateImageDataUri($this->authorized_signature);
    }

    public function getCardSignatureDataUriAttribute(): ?string
    {
        return static::legalRepresentative()?->authorized_signature_data_uri;
    }

    private function privateImageDataUri(?string $path): ?string
    {
        if (blank($path) || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('local')->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(Storage::disk('local')->get($path));
    }
}
