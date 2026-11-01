<div class="form-card glass-card slf" id="slf-card">
    @if($submitted)
        <div class="form-success">
            <div class="form-success-icon">✅</div>
            <h3>{{ $intent === 'visit' ? '¡Preferencia de visita recibida!' : '¡Solicitud recibida!' }}</h3>
            <p>
                @if($intent === 'visit')
                    Le confirmaremos la visita para <strong>{{ $serviceName }}</strong> por WhatsApp o llamada, según disponibilidad en su zona.
                @else
                    Un asesor de <strong>{{ $serviceName }}</strong> le contactará en menos de 2 horas hábiles.
                @endif
            </p>
            <button type="button" wire:click="$set('submitted', false)" class="btn btn-ghost" style="margin-top:16px;width:100%;justify-content:center;">Nueva solicitud</button>
        </div>
    @else
        <div class="form-tabs" role="tablist" aria-label="Tipo de solicitud">
            <button type="button" role="tab" class="form-tab {{ $intent === 'info' ? 'is-active' : '' }}" aria-selected="{{ $intent === 'info' ? 'true' : 'false' }}" wire:click="setIntent('info')">Solicitar información</button>
            <button type="button" role="tab" class="form-tab {{ $intent === 'visit' ? 'is-active' : '' }}" aria-selected="{{ $intent === 'visit' ? 'true' : 'false' }}" wire:click="setIntent('visit')">Agendar visita</button>
        </div>

        <span class="slf-service"><span class="slf-service__dot" aria-hidden="true"></span>{{ $serviceName }}</span>
        <h3>{{ $intent === 'visit' ? 'Agende su visita técnica gratuita' : $form['title'] }}</h3>
        <p class="form-sub">{{ $form['subtitle'] }}</p>

        <form wire:submit="submit" novalidate>
            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                <label for="slf-website">No llenar este campo</label>
                <input type="text" id="slf-website" wire:model="website" tabindex="-1" autocomplete="off" />
            </div>

            @foreach($form['questions'] ?? [] as $qKey => $question)
                <fieldset class="slf-question">
                    <legend>{{ $question['label'] }}</legend>
                    <div class="slf-pills">
                        @foreach($question['options'] as $option)
                            <label class="slf-pill">
                                <input type="radio" name="slf-{{ $qKey }}" value="{{ $option }}" wire:model="answers.{{ $qKey }}" />
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach

            <div class="form-row">
                <div class="form-group">
                    <label for="slf-name">Nombre *</label>
                    <input type="text" id="slf-name" wire:model="name" autocomplete="name" placeholder="Nombre y apellido" />
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="slf-phone">WhatsApp / teléfono *</label>
                    <input type="tel" id="slf-phone" wire:model="phone" autocomplete="tel" placeholder="300 000 0000" />
                    @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="slf-email">Correo electrónico</label>
                    <input type="email" id="slf-email" wire:model="email" autocomplete="email" placeholder="nombre@correo.com" />
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="slf-zone">Zona / municipio</label>
                    <select id="slf-zone" wire:model="zone">
                        <option value="">Seleccione…</option>
                        @foreach($zones as $z)
                            <option value="{{ $z }}">{{ $z }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($intent === 'visit')
                <div class="form-row">
                    <div class="form-group">
                        <label for="slf-date">Fecha preferida *</label>
                        <input type="date" id="slf-date" wire:model="preferred_visit_date" min="{{ now()->toDateString() }}" />
                        @error('preferred_visit_date') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="slf-slot">Franja horaria *</label>
                        <select id="slf-slot" wire:model="preferred_visit_slot">
                            <option value="">Seleccione…</option>
                            @foreach($visitSlots as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('preferred_visit_slot') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>
                <p class="form-hint">No es una cita confirmada: un asesor valida disponibilidad y le confirma el horario.</p>
            @endif

            <div class="form-group">
                <label for="slf-message">¿Algo más que debamos saber?</label>
                <textarea id="slf-message" wire:model="message" rows="3" placeholder="{{ $form['placeholder'] }}"></textarea>
            </div>

            <label class="slf-consent">
                <input type="checkbox" wire:model="consent" />
                <span>Autorizo a Smart Tech Security a tratar mis datos para responder esta solicitud, según la <a href="{{ route('privacidad') }}" target="_blank" rel="noopener">política de datos</a> (Ley 1581 de 2012). *</span>
            </label>
            @error('consent') <span class="form-error">{{ $message }}</span> @enderror

            <button type="submit" class="btn btn-primary form-submit" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ $intent === 'visit' ? 'Solicitar visita ✓' : 'Enviar solicitud ✓' }}</span>
                <span wire:loading wire:target="submit">Enviando…</span>
            </button>
        </form>
    @endif
</div>
