@php
    $state = collect([
        'first_names', 'last_names', 'position', 'photo_card', 'photo_cutout',
        'authorized_signature', 'signature_drawn', 'is_legal_representative',
        'portrait_scale', 'portrait_x', 'portrait_y',
    ])->mapWithKeys(fn (string $field): array => [$field => $get($field)])->all();
    $card = app(\App\Services\Employees\EmployeeCardViewData::class)->forForm($state, $record);
    $hasSavedCutout = filled($state['photo_cutout'] ?? null) || filled($record?->photo_cutout);
    $hasCardPhoto = filled($state['photo_card'] ?? null) || filled($record?->photo_card);
@endphp

@include('employees._card-styles')

<div class="employee-card-live-preview">
    @if($hasCardPhoto && ! $hasSavedCutout && blank($record?->id))
        <p class="employee-card-live-preview__hint">Guarda el empleado para recortar el fondo. Después ajusta la posición.</p>
    @elseif($hasCardPhoto && ! $hasSavedCutout)
        <p class="employee-card-live-preview__hint">Guarda los cambios para recortar el fondo de la foto nueva.</p>
    @else
        <p class="employee-card-live-preview__hint">Frente arriba, reverso abajo. Usa los controles para encajar la foto.</p>
    @endif

    <div class="employee-card-grid">
        @include('employees._card', ['card' => $card, 'showFaceLabels' => true])
    </div>
</div>

<style>
    .employee-card-live-preview {
        padding: .85rem;
        border: 1px solid rgb(229 231 235);
        border-radius: 1rem;
        background: linear-gradient(145deg, #f8fafc, #ecf8f5);
    }
    .employee-card-live-preview__hint {
        margin: 0 0 .75rem;
        color: #3d5a56;
        font-size: .8rem;
        line-height: 1.4;
    }
    .employee-card-live-preview .employee-card-grid { grid-template-columns: 1fr; gap: 1rem; }
    .employee-card-face-label {
        margin: 0 0 .35rem;
        color: #0b6b5f;
        font-size: .72rem;
        font-weight: 750;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .employee-card-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        justify-content: center;
        margin-top: .25rem;
    }
    .employee-card-action {
        display: inline-flex;
        min-height: 2.65rem;
        align-items: center;
        justify-content: center;
        padding: .7rem 1.15rem;
        border-radius: .65rem;
        font-size: .875rem;
        font-weight: 700;
        text-decoration: none;
    }
    .employee-card-action--primary { background: #118d7f; color: #fff; box-shadow: 0 .45rem 1rem rgba(17, 141, 127, .2); }
    .employee-card-action--secondary { border: 1px solid #118d7f; background: #fff; color: #08766b; }
    .employee-card-action--disabled { background: #e5e7eb; color: #6b7280; cursor: not-allowed; }
    .dark .employee-card-live-preview { border-color: rgb(55 65 81); background: linear-gradient(145deg, #111827, #0f2928); }
    .dark .employee-card-live-preview__hint,
    .dark .employee-card-face-label { color: #9ad0c8; }
</style>
