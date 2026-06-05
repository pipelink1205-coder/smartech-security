<div>
    <x-section
        tone="contact"
        tag="Contacto"
        subtitle="Estamos en Envigado pero llegamos a todo Medellín y el Valle de Aburrá. Respuesta garantizada en menos de 2 horas en horario laboral."
    >
        <x-slot:heading>
            <h2 class="section-title">Solicita tu <span class="accent">cotización gratis</span> hoy</h2>
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

            <div class="form-card glass-card">
                @if($submitted)
                    <div class="form-success">
                        <div class="form-success-icon">✅</div>
                        <h3>¡Solicitud enviada!</h3>
                        <p>Le contactaremos en menos de 2 horas. Revise su correo si dejó uno.</p>
                        <button wire:click="$set('submitted', false)" class="btn btn-primary" style="margin-top:16px;width:100%;justify-content:center;">
                            Nueva cotización
                        </button>
                    </div>
                @else
                    <h3>Solicitar Cotización Gratis</h3>
                    <p class="form-sub">Le respondemos en menos de 2 horas</p>

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
                                <select id="service" wire:model.live="service">
                                    <option value="">Seleccione…</option>
                                    @foreach(array_keys(config('quotes.pricing')) as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('service') <span class="form-error">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label for="zone">Zona / Municipio</label>
                                <select id="zone" wire:model.live="zone">
                                    <option value="">Seleccione…</option>
                                    @foreach(config('site.form_zones') as $z)
                                        <option value="{{ $z }}">{{ $z }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @if($showPreview)
                            <div class="price-preview">
                                <span class="price-preview-label">💰 Estimado preliminar:</span>
                                <span class="price-preview-range">
                                    ${{ number_format($priceMin, 0, ',', '.') }}
                                    – ${{ number_format($priceMax, 0, ',', '.') }} COP
                                </span>
                                <span class="price-preview-note">*Precio final tras visita técnica gratuita</span>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="message">Cuéntenos su proyecto</label>
                            <textarea id="message" wire:model="message" placeholder="Describa brevemente lo que necesita…"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary form-submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Enviar Solicitud ✓</span>
                            <span wire:loading>Enviando…</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-section>
</div>
