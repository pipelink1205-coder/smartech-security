<div class="itc glass-card glass-card--pad">
    <div class="itc-controls">
        <div class="form-group">
            <label for="itc-profile">Perfil que contratarías de planta</label>
            <select id="itc-profile" wire:model.live="profile">
                @foreach($this->profiles as $name => $salary)
                    <option value="{{ $name }}">{{ $name }} — ${{ number_format($salary, 0, ',', '.') }}/mes</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="itc-plan">Plan Smart Tech</label>
            <select id="itc-plan" wire:model.live="plan">
                @foreach($this->plans as $key => $planData)
                    <option value="{{ $key }}">{{ $planData['name'] }} — desde ${{ number_format($planData['monthly'], 0, ',', '.') }}/mes</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="itc-compare">
        <div class="itc-col itc-col--internal">
            <span class="itc-col-label">Empleado de TI de planta</span>
            <span class="itc-col-value">${{ number_format($this->employeeCost, 0, ',', '.') }}</span>
            <span class="itc-col-note">COP/mes · salario + prestaciones y cargas (≈{{ (int) round((config('quotes.it.payroll_factor', 1.53) - 1) * 100) }}% adicional)</span>
            <ul class="itc-col-list">
                <li>Salario base: ${{ number_format($this->salary, 0, ',', '.') }}</li>
                <li>Prima, cesantías, vacaciones</li>
                <li>Salud, pensión, ARL y parafiscales</li>
                <li>Una sola persona: vacaciones, incapacidades y rotación</li>
            </ul>
        </div>

        <div class="itc-vs" aria-hidden="true">vs</div>

        <div class="itc-col itc-col--smart">
            <span class="itc-col-label">Plan {{ $this->plans[$plan]['name'] ?? '' }} Smart Tech</span>
            <span class="itc-col-value">${{ number_format($this->planCost, 0, ',', '.') }}</span>
            <span class="itc-col-note">COP/mes · desde, según alcance del diagnóstico</span>
            <ul class="itc-col-list">
                <li>Equipo completo, no una sola persona</li>
                <li>SLA de respuesta por contrato</li>
                <li>Ciberseguridad incluida</li>
                <li>Sin prestaciones ni cargas laborales</li>
            </ul>
        </div>
    </div>

    @if($this->monthlySavings > 0)
        <div class="itc-result">
            <span class="itc-result-label">Ahorro estimado</span>
            <span class="itc-result-value">
                ${{ number_format($this->monthlySavings, 0, ',', '.') }} COP/mes
                <em>({{ $this->savingsPercent }}% menos · ${{ number_format($this->monthlySavings * 12, 0, ',', '.') }} al año)</em>
            </span>
            <a href="{{ route('contacto', ['intent' => 'visit']) }}" class="btn btn-primary">Agendar visita para validar tu ahorro</a>
        </div>
    @endif

    <p class="itc-disclaimer">*Cifras de referencia en COP. El valor final del plan se define con el diagnóstico gratuito según equipos, sedes y usuarios.</p>
</div>
