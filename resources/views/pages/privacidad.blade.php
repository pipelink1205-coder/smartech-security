<x-app-layout :title="'Política de Privacidad – Smart Tech Security'">
    <article class="legal container">
        <header class="legal-head">
            <h1>Política de privacidad y tratamiento de datos personales</h1>
            <p class="legal-updated">Última actualización: {{ config('legal.updated_at', 'julio de 2026') }}</p>
        </header>

        <section class="legal-block">
            <h2>1. Responsable del tratamiento</h2>
            <p>Smart Tech Security, con domicilio en Envigado, Antioquia (Colombia), es responsable del tratamiento de los datos personales recolectados a través de este sitio web, en cumplimiento de la Ley 1581 de 2012 y sus decretos reglamentarios.</p>
            <p>Canales de contacto: correo <a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a> y WhatsApp <a href="https://wa.me/{{ config('contact.whatsapp') }}" target="_blank" rel="noopener">{{ config('contact.whatsapp_formatted') }}</a>.</p>
        </section>

        <section class="legal-block">
            <h2>2. Datos que recolectamos</h2>
            <p>A través de los formularios de cotización, diagnóstico y el contacto por WhatsApp recolectamos: nombre, teléfono, correo electrónico, servicio de interés, nombre de la empresa, número de empleados, información sobre la situación tecnológica y el mensaje que usted nos escriba.</p>
        </section>

        <section class="legal-block">
            <h2>3. Finalidad del tratamiento</h2>
            <ul>
                <li>Responder solicitudes de cotización y diagnóstico y hacer seguimiento comercial.</li>
                <li>Prestar y mejorar los servicios contratados.</li>
                <li>Enviar información sobre el estado de su solicitud por correo o WhatsApp.</li>
                <li>Cumplir obligaciones legales y contractuales.</li>
            </ul>
            <p>No vendemos ni compartimos sus datos con terceros con fines publicitarios.</p>
        </section>

        <section class="legal-block">
            <h2>4. Derechos del titular</h2>
            <p>Como titular de los datos usted puede conocer, actualizar, rectificar y solicitar la supresión de sus datos personales, así como revocar la autorización otorgada, en los términos de la Ley 1581 de 2012. Para ejercer estos derechos escríbanos a <a href="mailto:{{ config('contact.email') }}">{{ config('contact.email') }}</a> indicando su solicitud; responderemos dentro de los plazos legales.</p>
        </section>

        <section class="legal-block">
            <h2>5. Seguridad y conservación</h2>
            <p>Aplicamos medidas técnicas y organizativas razonables para proteger su información contra acceso no autorizado, pérdida o alteración. Los datos se conservan mientras exista una relación comercial o mientras sean necesarios para las finalidades descritas.</p>
        </section>

        <section class="legal-block">
            <h2>6. Imágenes de videovigilancia</h2>
            <p>En los proyectos de videovigilancia que instalamos, el responsable del tratamiento de las imágenes captadas es el cliente propietario del sistema. Smart Tech Security asesora la señalización y configuración conforme a la normativa de protección de datos.</p>
        </section>

        <section class="legal-block">
            <h2>7. Cambios a esta política</h2>
            <p>Podemos actualizar esta política para reflejar cambios normativos o del servicio. La versión vigente estará siempre publicada en esta página.</p>
        </section>
    </article>
</x-app-layout>
