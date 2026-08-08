<x-app-layout :title="'Política de Ventas – Smart Tech Security'">
    <article class="legal container">
        <header class="legal-head">
            <h1>Política de ventas</h1>
            <p class="legal-updated">Última actualización: {{ config('legal.updated_at', 'agosto de 2026') }}</p>
        </header>

        <section class="legal-block">
            <h2>1. Alcance</h2>
            <p>Esta política aplica a la venta e instalación de equipos y servicios de seguridad electrónica, redes, energía solar, domótica, outsourcing de TI y demás servicios ofrecidos por Smart Tech Security, ya sea a través del sitio web, WhatsApp, correo electrónico o visita comercial presencial.</p>
        </section>

        <section class="legal-block">
            <h2>2. Proceso de compra</h2>
            <ul>
                <li>El cliente solicita una cotización a través del sitio, WhatsApp o correo, indicando sus necesidades.</li>
                <li>Smart Tech Security realiza, cuando aplica, una visita técnica o diagnóstico remoto para precisar el alcance.</li>
                <li>Se entrega una cotización formal con el detalle de equipos, servicios, tiempos de entrega y forma de pago.</li>
                <li>La venta se formaliza con la aceptación por escrito de la cotización (correo, WhatsApp o firma) y, cuando se requiera, el pago del anticipo pactado.</li>
            </ul>
        </section>

        <section class="legal-block">
            <h2>3. Precios y vigencia de cotizaciones</h2>
            <p>Los precios se expresan en pesos colombianos (COP) e incluyen o excluyen IVA según se indique expresamente en cada cotización. Las cotizaciones tienen la vigencia señalada en el documento; superado ese plazo, Smart Tech Security puede actualizar precios por variaciones en costos de equipos, dólar o mano de obra.</p>
        </section>

        <section class="legal-block">
            <h2>4. Formas de pago</h2>
            <p>Las formas de pago aceptadas (transferencia bancaria, consignación, pago electrónico u otras) y la distribución de anticipos y saldos se acuerdan en cada cotización o contrato, según el tipo y tamaño del proyecto. Para proyectos de instalación, es habitual un anticipo antes del inicio de obra y un saldo contra entrega o acta de recibido a satisfacción.</p>
        </section>

        <section class="legal-block">
            <h2>5. Tiempos de entrega e instalación</h2>
            <p>Los tiempos de entrega e instalación se informan en la cotización y dependen de la disponibilidad de equipos, las condiciones del sitio y factores externos (importaciones, clima, permisos). Cualquier cambio relevante en el cronograma será comunicado oportunamente al cliente.</p>
        </section>

        <section class="legal-block">
            <h2>6. Cambios, devoluciones y cancelaciones</h2>
            <ul>
                <li>Los equipos ya instalados o personalizados según las necesidades del cliente no son objeto de devolución, salvo falla de fábrica cubierta por garantía.</li>
                <li>Las cancelaciones de un proyecto ya iniciado pueden generar el cobro de los costos y anticipos ya ejecutados (equipos comprados, mano de obra realizada, visitas técnicas).</li>
                <li>Toda solicitud de cambio, devolución o cancelación debe enviarse por escrito a los canales de contacto de Smart Tech Security.</li>
            </ul>
        </section>

        <section class="legal-block">
            <h2>7. Garantías</h2>
            <p>Los equipos e instalaciones cuentan con garantía de fábrica y garantía de mano de obra, en los términos indicados en la factura o contrato de cada proyecto. Los detalles de cobertura y exclusiones se describen en nuestros <a href="{{ route('terminos') }}">términos y condiciones</a>.</p>
        </section>

        <section class="legal-block">
            <h2>8. Datos personales</h2>
            <p>Los datos suministrados durante el proceso de venta se tratan conforme a nuestra <a href="{{ route('privacidad') }}">política de privacidad y tratamiento de datos</a>.</p>
        </section>

        <section class="legal-block">
            <h2>9. Contacto</h2>
            <p>Para dudas sobre el proceso de compra escríbanos a <a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a> o por WhatsApp al <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener">{{ config('contact.whatsapp_formatted') }}</a>.</p>
        </section>
    </article>
</x-app-layout>
