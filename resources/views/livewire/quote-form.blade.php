<div>
    <x-section
        tone="contact"
        tag="Contacto"
        subtitle="Estamos en Envigado y cubrimos Medellín y el Valle de Aburrá. La visita técnica es gratuita; la cotización detallada se entrega después del diagnóstico."
    >
        <x-slot:heading>
            <h2 class="section-title">Solicita <span class="accent">información</span> o agenda tu visita</h2>
        </x-slot:heading>

        <div class="contacto-grid">
            <div class="contacto-text">
                <div class="contacto-info">
                    <div class="contacto-info-item">
                        <div class="contacto-info-icon">📍</div>
                        <span>{{ config('contact.address') }}</span>
                    </div>
                    <div class="contacto-info-item">
                        <div class="contacto-info-icon">📞</div>
                        <span>
                            <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline;">
                                {{ config('contact.whatsapp_formatted') }}
                            </a>
                            @if(config('contact.whatsapp_secondary'))
                                &nbsp;·&nbsp;
                                <a href="https://wa.me/{{ config('contact.whatsapp_secondary') }}" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline;">
                                    {{ config('contact.whatsapp_secondary_formatted') }}
                                </a>
                            @endif
                        </span>
                    </div>
                    <div class="contacto-info-item">
                        <div class="contacto-info-icon">✉️</div>
                        <span>
                            <a href="mailto:{{ config('contact.email') }}" style="color:inherit;text-decoration:underline;">
                                {{ config('contact.email') }}
                            </a>
                            &nbsp;·&nbsp;
                            <a href="mailto:{{ config('contact.admin_email') }}" style="color:inherit;text-decoration:underline;">
                                {{ config('contact.admin_email') }}
                            </a>
                        </span>
                    </div>
                    <div class="contacto-info-item">
                        <div class="contacto-info-icon">🕐</div>
                        <span>{{ config('contact.hours') }} · {{ config('contact.support_note') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-card glass-card" id="quote-form-card">
                @if($submitted)
                    <div class="form-success">
                        <div class="form-success-icon">✅</div>
                        <h3>{{ $intent === 'visit' ? '¡Preferencia de visita recibida!' : '¡Solicitud recibida!' }}</h3>
                        <p>
                            @if($intent === 'visit')
                                Le confirmaremos la visita por WhatsApp o llamada según disponibilidad en su zona (normalmente en menos de 2 horas hábiles).
                            @else
                                Le contactaremos en menos de 2 horas hábiles para ampliar información o agendar la visita técnica gratuita.
                            @endif
                        </p>
                        <button wire:click="$set('submitted', false)" class="btn btn-primary" style="margin-top:16px;width:100%;justify-content:center;">
                            Nueva solicitud
                        </button>
                    </div>
                @else
                    <div class="form-tabs" role="tablist" aria-label="Tipo de solicitud">
                        <button
                            type="button"
                            role="tab"
                            class="form-tab {{ $intent === 'info' ? 'is-active' : '' }}"
                            aria-selected="{{ $intent === 'info' ? 'true' : 'false' }}"
                            wire:click="setIntent('info')"
                        >
                            Solicitar información
                        </button>
                        <button
                            type="button"
                            role="tab"
                            class="form-tab {{ $intent === 'visit' ? 'is-active' : '' }}"
                            aria-selected="{{ $intent === 'visit' ? 'true' : 'false' }}"
                            wire:click="setIntent('visit')"
                        >
                            Agendar visita
                        </button>
                    </div>

                    <h3>{{ $intent === 'visit' ? 'Preferencia de visita' : 'Solicitar información' }}</h3>
                    <p class="form-sub">
                        @if($intent === 'visit')
                            Indique fecha y franja. Confirmamos disponibilidad por WhatsApp · Cotización tras el diagnóstico
                        @else
                            Visita técnica gratuita · Cotización tras el diagnóstico
                        @endif
                    </p>

                    <form wire:submit="submit">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Nombre completo *</label>
                                <input type="text" id="name" wire:model="name" placeholder="Juan García" />
                                @error('name') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">Teléfono / WhatsApp *</label>
                                <input type="tel" id="phone" wire:model="phone" placeholder="+57 {{ config('contact.whatsapp_formatted') }}" />
                                @error('phone') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" wire:model="email" placeholder="juan@empresa.com" />
                            @error('email') <span class="form-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="service">Servicio de interés *</label>
                                <select id="service" wire:model="service">
                                    <option value="">Seleccione…</option>
                                    @foreach($serviceOptions as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('service') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="zone">Zona / Municipio</label>
                                <select id="zone" wire:model="zone">
                                    <option value="">Seleccione…</option>
                                    @foreach(config('site.form_zones') as $z)
                                        <option value="{{ $z }}">{{ $z }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($intent === 'visit')
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="preferred_visit_date">Fecha preferida *</label>
                                    <input type="date" id="preferred_visit_date" wire:model="preferred_visit_date" min="{{ now()->toDateString() }}" />
                                    @error('preferred_visit_date') <span class="form-error">{{ $message }}</span> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="preferred_visit_slot">Franja horaria *</label>
                                    <select id="preferred_visit_slot" wire:model="preferred_visit_slot">
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
                            <label for="message">Cuéntenos qué necesita</label>
                            <textarea id="message" wire:model="message" placeholder="Ej.: redes en una oficina, cámaras en casa, soporte TI…"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary form-submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                {{ $intent === 'visit' ? 'Solicitar visita ✓' : 'Enviar solicitud ✓' }}
                            </span>
                            <span wire:loading>Enviando…</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-section>
</div>
