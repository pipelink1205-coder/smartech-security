<x-app-layout :title="'Términos y Condiciones – Smart Tech Security'">
    <article class="legal container">
        <header class="legal-head">
            <h1>Términos y condiciones</h1>
            <p class="legal-updated">Última actualización: {{ config('legal.updated_at', 'agosto de 2026') }}</p>
        </header>

        <section class="legal-block">
            <h2>1. Aceptación de los términos</h2>
            <p>Estos términos y condiciones regulan el uso del sitio web de Smart Tech Security y la relación comercial entre Smart Tech Security (con domicilio en Envigado, Antioquia, Colombia) y las personas naturales o jurídicas que soliciten cotizaciones, diagnósticos o contraten alguno de nuestros servicios. Al navegar en este sitio o enviar un formulario de contacto, usted acepta los términos aquí descritos.</p>
        </section>

        <section class="legal-block">
            <h2>2. Servicios ofrecidos</h2>
            <p>Smart Tech Security presta servicios de outsourcing de TI, ciberseguridad, cámaras de seguridad y videovigilancia, alarmas, control de acceso, domótica, energía solar, redes e internet, IPTV para hoteles y demás servicios publicados en el catálogo del sitio. El alcance específico, tiempos y precios de cada proyecto se formalizan en una cotización o contrato particular.</p>
        </section>

        <section class="legal-block">
            <h2>3. Cotizaciones y visitas técnicas</h2>
            <ul>
                <li>Las cotizaciones generadas a través del sitio son estimaciones basadas en la información suministrada por el cliente y pueden ajustarse tras una visita técnica o levantamiento de información en sitio.</li>
                <li>Las cotizaciones tienen una vigencia limitada, indicada en el documento correspondiente; transcurrido ese plazo los precios pueden variar.</li>
                <li>Agendar una visita a través del sitio no genera obligación de compra para el cliente ni de ejecución inmediata para Smart Tech Security antes de confirmar los términos comerciales.</li>
            </ul>
        </section>

        <section class="legal-block">
            <h2>4. Instalación y garantías</h2>
            <p>Los equipos e instalaciones cuentan con la garantía indicada por el fabricante y, adicionalmente, con la garantía de mano de obra que se especifique en la factura o contrato de cada proyecto. La garantía no cubre daños ocasionados por manipulación indebida por terceros, eventos de fuerza mayor, variaciones eléctricas ajenas a la instalación, ni mantenimiento realizado por personal no autorizado por Smart Tech Security.</p>
        </section>

        <section class="legal-block">
            <h2>5. Obligaciones del cliente</h2>
            <ul>
                <li>Suministrar información veraz y completa para la elaboración de cotizaciones y la ejecución del proyecto.</li>
                <li>Garantizar el acceso a las instalaciones y la disponibilidad de servicios básicos (energía, internet, espacio físico) necesarios para la instalación.</li>
                <li>Cumplir con las condiciones de pago acordadas en la cotización o contrato.</li>
            </ul>
        </section>

        <section class="legal-block">
            <h2>6. Propiedad intelectual</h2>
            <p>El contenido del sitio (textos, imágenes, logotipos, marcas y diseño) es propiedad de Smart Tech Security o de sus respectivos titulares y está protegido por la normativa de propiedad intelectual vigente en Colombia. Queda prohibida su reproducción total o parcial sin autorización previa y escrita.</p>
        </section>

        <section class="legal-block">
            <h2>7. Limitación de responsabilidad</h2>
            <p>Smart Tech Security no será responsable por interrupciones del sitio web ajenas a su control, ni por el uso que terceros no autorizados hagan de los sistemas instalados. La responsabilidad frente a fallas en equipos o servicios contratados se limita a lo establecido en la garantía correspondiente y en el contrato de cada proyecto.</p>
        </section>

        <section class="legal-block">
            <h2>8. Ley aplicable y jurisdicción</h2>
            <p>Estos términos se rigen por las leyes de la República de Colombia. Cualquier controversia derivada de su interpretación o aplicación se someterá a los jueces competentes de Medellín y el Valle de Aburrá, sin perjuicio de los mecanismos de solución directa que las partes puedan acordar.</p>
        </section>

        <section class="legal-block">
            <h2>9. Cambios a estos términos</h2>
            <p>Smart Tech Security puede actualizar estos términos para reflejar cambios normativos, comerciales o del servicio. La versión vigente estará siempre publicada en esta página.</p>
        </section>

        <section class="legal-block">
            <h2>10. Contacto</h2>
            <p>Para dudas sobre estos términos, escríbanos a <a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a> o por WhatsApp al <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener">{{ config('contact.whatsapp_formatted') }}</a>.</p>
        </section>
    </article>
</x-app-layout>
