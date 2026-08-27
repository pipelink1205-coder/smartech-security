<div class="employee-card-actions">
    @if(filled($record?->id))
        @if($record->status === 'active')
            <a class="employee-card-action employee-card-action--primary" href="{{ route('admin.employees.card-pdf', $record) }}">
                Generar carnet PDF
            </a>
        @else
            <span class="employee-card-action employee-card-action--disabled">Activa el empleado para generar</span>
        @endif
        <a class="employee-card-action employee-card-action--secondary" href="{{ route('employees.verify', ['employee' => $record->verification_token]) }}" target="_blank" rel="noopener">
            Verificar QR
        </a>
    @else
        <span class="employee-card-action employee-card-action--disabled">Guarda el empleado para generar</span>
    @endif
</div>
