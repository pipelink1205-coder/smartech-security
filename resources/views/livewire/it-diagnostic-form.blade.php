<div class="form-card glass-card" id="diagnostico">
    @if($submitted)
        <div class="form-success">
            <div class="form-success-icon">✅</div>
            <h3>¡Solicitud recibida!</h3>
            <p>Agendaremos su diagnóstico gratuito en menos de 2 horas hábiles. Revise su correo si dejó uno.</p>
            <button wire:click="$set('submitted', false)" class="btn btn-primary" style="margin-top:16px;width:100%;justify-content:center;">
                Enviar otra solicitud
            </button>
        </div>
    @else
        <h3>Solicita tu diagnóstico gratuito</h3>
        <p class="form-sub">Sin costo y sin compromiso. Le respondemos en menos de 2 horas hábiles.</p>

        <form wire:submit="submit">
            {{-- Honeypot anti-spam: oculto para humanos --}}
            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                <label for="website">No llenar este campo</label>
                <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dg-name">Nombre completo *</label>
                    <input type="text" id="dg-name" wire:model="name" placeholder="Juan García" />
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="dg-company">Empresa *</label>
                    <input type="text" id="dg-company" wire:model="company" placeholder="Mi Empresa S.A.S." />
                    @error('company') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dg-employees">Número de empleados *</label>
                    <select id="dg-employees" wire:model="employees_range">
                        <option value="">Seleccione…</option>
                        @foreach($employeesRanges as $range)
                            <option value="{{ $range }}">{{ $range }}</option>
                        @endforeach
                    </select>
                    @error('employees_range') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="dg-current">¿Cómo manejan TI hoy? *</label>
                    <select id="dg-current" wire:model="current_it">
                        <option value="">Seleccione…</option>
                        @foreach($currentItOptions as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('current_it') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="dg-phone">Teléfono / WhatsApp *</label>
                    <input type="tel" id="dg-phone" wire:model="phone" placeholder="+57 300 000 0000" />
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="dg-email">Correo electrónico</label>
                    <input type="email" id="dg-email" wire:model="email" placeholder="juan@empresa.com" />
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="dg-message">Cuéntenos su situación</label>
                <textarea id="dg-message" wire:model="message" placeholder="Ej.: se nos cae la red, no tenemos copias de seguridad, el correo vive lleno…"></textarea>
                @error('message') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary form-submit" wire:loading.attr="disabled">
                <span wire:loading.remove>Solicitar diagnóstico gratuito ✓</span>
                <span wire:loading>Enviando…</span>
            </button>
        </form>
    @endif
</div>
