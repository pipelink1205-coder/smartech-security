@php
    $waServices = \App\Models\Service::active()->ordered()->pluck('name');
@endphp
<div id="wa-lead-modal" class="wa-modal" hidden
     data-store-url="{{ route('whatsapp-leads.store') }}"
     role="presentation">
    <div class="wa-modal-backdrop" data-wa-close></div>
    <div class="wa-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="wa-modal-title">
        <button type="button" class="wa-modal-close" data-wa-close aria-label="Cerrar">&times;</button>
        <h2 id="wa-modal-title">Escríbenos por WhatsApp</h2>
        <p class="wa-modal-lead">Déjanos tu nombre y teléfono para atenderte. Después se abre el chat con el mensaje listo.</p>

        <form id="wa-lead-form" novalidate>
            <div class="wa-hp" aria-hidden="true">
                <label for="wa-website">Sitio web</label>
                <input type="text" id="wa-website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="form-group">
                <label for="wa-name">Nombre *</label>
                <input type="text" id="wa-name" name="name" autocomplete="name" required maxlength="100" placeholder="Tu nombre">
            </div>
            <div class="form-group">
                <label for="wa-phone">Teléfono / WhatsApp *</label>
                <input type="tel" id="wa-phone" name="phone" autocomplete="tel" required maxlength="20" placeholder="300 123 4567">
            </div>
            <div class="form-group">
                <label for="wa-service">Servicio de interés *</label>
                <select id="wa-service" name="service" required>
                    <option value="">Seleccione…</option>
                    @foreach($waServices as $name)
                        <option value="{{ $name }}">{{ $name }}</option>
                    @endforeach
                    <option value="Varios servicios">Varios servicios</option>
                    <option value="No estoy seguro">No estoy seguro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="wa-message">Mensaje <span>(opcional)</span></label>
                <textarea id="wa-message" name="message" maxlength="500" rows="2" placeholder="¿Qué necesitas instalar o cotizar?"></textarea>
            </div>

            <p class="wa-modal-error" id="wa-lead-error" hidden></p>

            <button type="submit" class="btn btn-primary form-submit" id="wa-lead-submit">Continuar a WhatsApp</button>
            <p class="wa-modal-privacy">Usamos estos datos para contactarte. <a href="{{ route('privacidad') }}">Política de privacidad</a>.</p>
        </form>
    </div>
</div>
