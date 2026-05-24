<section class="contacto" id="contacto">
  <div class="container">
    <div class="contacto-grid">

      {{-- Info izquierda --}}
      <div class="contacto-text">
        <span class="section-tag">Contacto</span>
        <h2 class="section-title">¿Listo para proteger su <span style="color:#4ade80">espacio?</span></h2>
        <p>Solicite una visita técnica sin costo. Evaluamos su propiedad y entregamos una cotización detallada en menos de 24 horas.</p>
        <div class="contacto-info">
          <div class="contacto-info-item">
            <div class="contacto-info-icon">📞</div>
            <span>+57 304 XXX XXXX &nbsp;·&nbsp; Lun–Sáb 7am–7pm</span>
          </div>
          <div class="contacto-info-item">
            <div class="contacto-info-icon">✉️</div>
            <span>info@smarttechsecurity.com.co</span>
          </div>
          <div class="contacto-info-item">
            <div class="contacto-info-icon">📍</div>
            <span>Medellín, Antioquia – Colombia</span>
          </div>
          <div class="contacto-info-item">
            <div class="contacto-info-icon">💬</div>
            <span>WhatsApp disponible las 24 horas</span>
          </div>
        </div>
      </div>

      {{-- Formulario reactivo --}}
      <div class="form-card">

        @if($submitted)
          {{-- Estado: enviado exitosamente --}}
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
                <input type="tel" id="phone" wire:model="phone" placeholder="+57 300 000 0000" />
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
                  @foreach(config('quotes.pricing') as $name => $prices)
                    <option value="{{ $name }}">{{ $name }}</option>
                  @endforeach
                </select>
                @error('service') <span class="form-error">{{ $message }}</span> @enderror
              </div>
              <div class="form-group">
                <label for="zone">Zona / Municipio</label>
                <select id="zone" wire:model.live="zone">
                  <option value="">Seleccione…</option>
                  @foreach(array_keys(config('quotes.zone_surcharge')) as $z)
                    <option value="{{ $z }}">{{ $z }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- Precio estimado en tiempo real --}}
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
  </div>
</section>
