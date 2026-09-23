<x-app-layout
    :title="'Desarrollo de Software a la Medida | Smart Tech Security'"
    :description="'Desarrollo de software a la medida, aplicaciones web, automatización, inteligencia artificial, analítica de datos e integraciones para empresas.'"
    :canonical="route('servicios.show', 'desarrollo-software')"
>
    @php
        $contactUrl = route('contacto', ['servicio' => $service->name]);
        $whatsappMessage = 'Hola, estoy interesado en una solución de desarrollo de software para mi empresa y quisiera recibir asesoría.';
        $problems = [
            'Información dispersa en archivos Excel',
            'Procesos administrativos manuales',
            'Digitación repetitiva',
            'Dependencia de documentos físicos',
            'Reportes elaborados manualmente',
            'Información repartida en diferentes sistemas',
            'Falta de indicadores actualizados',
            'Procesos sin trazabilidad',
            'Sistemas que no se comunican entre sí',
        ];
        $solutions = [
            [
                'title' => 'Software a la medida',
                'text' => 'Sistemas empresariales diseñados según los procesos y reglas de negocio de cada organización.',
                'items' => ['Sistemas administrativos', 'ERP y módulos empresariales', 'Gestión documental', 'Tesorería y compras', 'Gestión humana e inventarios', 'Operaciones y flujos de aprobación', 'Sistemas especializados'],
            ],
            [
                'title' => 'Aplicaciones web',
                'text' => 'Plataformas empresariales accesibles desde el navegador para centralizar procesos, información y usuarios.',
                'items' => ['Acceso desde diferentes dispositivos', 'Experiencias responsive', 'Roles y flujos según el proyecto'],
            ],
            [
                'title' => 'Desarrollo web',
                'text' => 'Sitios y herramientas web que presentan la empresa y facilitan sus procesos digitales.',
                'items' => ['Sitios corporativos', 'Landing pages y portales', 'Catálogos y formularios', 'Integraciones web'],
            ],
            [
                'title' => 'Automatización e Inteligencia Artificial',
                'text' => 'Digitalización de tareas repetitivas y apoyo inteligente a procesos donde exista un caso de uso adecuado.',
                'items' => ['Automatización de tareas', 'OCR y extracción de información', 'Clasificación documental', 'Asistentes empresariales', 'Generación de informes'],
            ],
            [
                'title' => 'Analítica de datos y Business Intelligence',
                'text' => 'Integración y organización de datos para construir indicadores que ayuden a entender la operación.',
                'items' => ['Power BI y dashboards', 'KPIs y modelos de datos', 'ETL y SQL', 'Integración de fuentes', 'Reportes automatizados'],
            ],
            [
                'title' => 'Integraciones y APIs',
                'text' => 'Conexión entre las herramientas existentes para reducir reprocesos y facilitar el intercambio de información.',
                'items' => ['ERP y sistemas contables', 'Facturación electrónica', 'Bases de datos y APIs', 'Correo y WhatsApp', 'Sistemas de terceros'],
            ],
        ];
        $aiUses = [
            ['title' => 'OCR inteligente', 'text' => 'Extracción de información desde facturas, formularios y documentos.'],
            ['title' => 'Procesamiento documental', 'text' => 'Clasificación y organización de información según el proceso.'],
            ['title' => 'Generación de informes', 'text' => 'Automatización de reportes a partir de información operacional.'],
            ['title' => 'Asistentes empresariales', 'text' => 'Consultas sobre información o documentación cuando la arquitectura y los permisos lo permitan.'],
            ['title' => 'Automatización inteligente', 'text' => 'Apoyo a tareas repetitivas mediante reglas, integraciones e inteligencia artificial.'],
        ];
        $method = [
            ['Diagnóstico', 'Entendemos la necesidad, los objetivos y el contexto del proyecto.'],
            ['Levantamiento de procesos', 'Documentamos actores, reglas, tareas, datos y excepciones.'],
            ['Diseño y prototipo', 'Acordamos flujos y estructura antes de construir la solución.'],
            ['Desarrollo', 'Implementamos las funcionalidades priorizadas y sus integraciones.'],
            ['Pruebas', 'Revisamos el comportamiento y corregimos hallazgos técnicos.'],
            ['Validación con usuarios (UAT)', 'Los usuarios revisan los flujos frente a sus necesidades.'],
            ['Implementación', 'Preparamos el despliegue según la arquitectura acordada.'],
            ['Capacitación', 'Orientamos a los usuarios sobre el uso de la solución.'],
            ['Soporte y evolución', 'Atendemos ajustes y nuevas necesidades según el alcance acordado.'],
        ];
        $useCases = [
            ['title' => 'Sistemas de gestión empresarial', 'text' => 'Centralización de procesos administrativos y operativos.'],
            ['title' => 'Gestión documental', 'text' => 'Documentos, trazabilidad, clasificación, aprobaciones y automatización.'],
            ['title' => 'Sistemas para operación en campo', 'text' => 'Asignaciones, formularios, seguimiento e información operacional.'],
            ['title' => 'Sistemas de parqueaderos', 'text' => 'Ingreso, salida, tarifas, tickets, control y reportería.'],
            ['title' => 'Dashboards gerenciales', 'text' => 'Indicadores construidos a partir de diferentes fuentes de información.'],
            ['title' => 'Automatización administrativa', 'text' => 'Procesamiento de información, validaciones, flujos y generación de reportes.'],
        ];
        $technologyGroups = [
            ['title' => 'Backend', 'items' => ['Laravel / PHP', 'Python']],
            ['title' => 'Frontend', 'items' => ['JavaScript', 'React', 'Next.js', 'HTML / CSS']],
            ['title' => 'Datos', 'items' => ['SQL Server', 'MySQL', 'PostgreSQL', 'Power BI']],
            ['title' => 'Infraestructura', 'items' => ['Docker', 'Linux', 'Windows Server', 'VPS / Cloud']],
            ['title' => 'Desarrollo y DevOps', 'items' => ['Git', 'GitHub', 'APIs']],
            ['title' => 'Inteligencia Artificial', 'items' => ['Integración con modelos de IA', 'OCR', 'Procesamiento documental', 'Automatización']],
        ];
    @endphp

    <article class="sd software-page" data-wa-service="{{ $service->name }}">
        <header class="sd-hero software-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('servicios') }}">Servicios</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Desarrollo de Software</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker">
                            <x-service-icon :icon="$service->icon" :name="$service->name" size="sm" class="sd-hero-kicker-icon" />
                            Software, automatización y datos
                        </span>
                        <h1 class="sd-hero-title">Software que se adapta a tu empresa, no tu empresa al software.</h1>
                        <p class="sd-hero-tagline">Diseñamos aplicaciones web, sistemas empresariales, automatizaciones y soluciones de analítica de datos construidas alrededor de los procesos reales de tu organización.</p>
                        <p class="software-hero-topics">Software a medida · Aplicaciones Web · Automatización · Inteligencia Artificial · Analítica de Datos · Integraciones</p>
                        <div class="sd-hero-actions">
                            <a href="{{ $contactUrl }}" class="btn btn-primary">Solicitar diagnóstico</a>
                            <x-whatsapp-link from="service_hero" :message="$whatsappMessage" class="btn sd-btn-outline" target="_blank">Hablar con un especialista</x-whatsapp-link>
                        </div>
                    </div>

                    <figure class="sd-hero-media">
                        <img src="{{ $service->image_url }}" alt="Ilustración de una aplicación web conectada con una base de datos y un dashboard" width="880" height="550" fetchpriority="high" />
                    </figure>
                </div>
            </div>
        </header>

        <x-section tone="muted" class="software-section" tag="Retos de operación" title="¿Tu empresa todavía trabaja así?">
            <ul class="software-problems">
                @foreach($problems as $problem)
                    <li class="software-problem glass-card"><span aria-hidden="true">!</span>{{ $problem }}</li>
                @endforeach
            </ul>
            <div class="software-problems-callout">
                <h3>Podemos digitalizarlo, integrarlo y automatizarlo.</h3>
                <p>Analizamos cómo funciona actualmente tu operación y diseñamos soluciones tecnológicas adaptadas a tus procesos.</p>
            </div>
        </x-section>

        <x-section tone="default" class="software-section" tag="Soluciones" title="Tecnología alineada con tu operación" subtitle="Cada proyecto parte de una necesidad concreta. El alcance se define según los procesos, usuarios, datos e integraciones involucrados.">
            <div class="software-solutions">
                @foreach($solutions as $index => $solution)
                    <article class="software-card glass-card">
                        <span class="software-card-index" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $solution['title'] }}</h3>
                        <p>{{ $solution['text'] }}</p>
                        <ul class="software-check-list">
                            @foreach($solution['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </x-section>

        <x-section tone="dark" class="software-section software-analytics" tag="Analítica de datos" title="Convierte tus datos en decisiones" subtitle="Integramos información proveniente de diferentes fuentes y la transformamos en indicadores claros para apoyar la toma de decisiones.">
            <ol class="software-data-flow" aria-label="Flujo desde las fuentes de datos hasta los indicadores">
                <li><span class="software-flow-label">Fuentes</span><strong>ERP + Excel + Bases de Datos + APIs</strong></li>
                <li><span class="software-flow-label">Preparación</span><strong>Integración y procesamiento</strong></li>
                <li><span class="software-flow-label">Visualización</span><strong>Dashboards / Power BI</strong></li>
                <li><span class="software-flow-label">Uso</span><strong>Indicadores para la toma de decisiones</strong></li>
            </ol>
        </x-section>

        <x-section tone="alt" class="software-section" tag="Inteligencia Artificial" title="Inteligencia Artificial aplicada a procesos reales" subtitle="Evaluamos dónde puede aportar valor y cómo integrarla con los procesos, sistemas y permisos de la organización.">
            <div class="software-ai-grid">
                @foreach($aiUses as $use)
                    <article class="software-ai-card glass-card">
                        <x-service-icon icon="code" :name="$use['title']" size="sm" />
                        <h3>{{ $use['title'] }}</h3>
                        <p>{{ $use['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </x-section>

        <x-section tone="default" class="software-section software-ecosystem" tag="Acompañamiento tecnológico" title="Un ecosistema tecnológico para tu empresa" subtitle="El desarrollo de software puede integrarse con la infraestructura, seguridad, conectividad y soporte tecnológico de tu organización. Son capacidades complementarias; cada proyecto define qué necesita.">
            <ol class="software-ecosystem-flow">
                @foreach(['Infraestructura', 'Redes y conectividad', 'Ciberseguridad', 'Software empresarial', 'Bases de datos', 'Automatización e IA', 'Analítica', 'Outsourcing y soporte TI'] as $layer)
                    <li>{{ $layer }}</li>
                @endforeach
            </ol>
        </x-section>

        <x-section tone="muted" class="software-section" tag="Metodología" title="De la necesidad a una solución funcional" subtitle="Avanzamos por etapas para validar el alcance, los flujos y la solución con las personas que la utilizarán.">
            <ol class="software-method">
                @foreach($method as $index => $step)
                    <li class="software-method-step glass-card">
                        <span class="software-method-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $step[0] }}</h3>
                        <p>{{ $step[1] }}</p>
                    </li>
                @endforeach
            </ol>
        </x-section>

        <x-section tone="default" class="software-section" tag="Tipos de solución" title="Soluciones que podemos construir" subtitle="Estos escenarios ilustran tipos de solución posibles; no representan clientes ni proyectos atribuidos.">
            <div class="software-use-cases">
                @foreach($useCases as $case)
                    <article class="software-use-case glass-card">
                        <h3>{{ $case['title'] }}</h3>
                        <p>{{ $case['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </x-section>

        <x-section tone="alt" class="software-section" tag="Respaldo técnico" title="Tecnología al servicio de la solución" subtitle="La selección depende de los requerimientos funcionales, técnicos y operativos de cada proyecto.">
            <div class="software-tech-groups">
                @foreach($technologyGroups as $group)
                    <section class="software-tech-group glass-card" aria-label="{{ $group['title'] }}">
                        <h3>{{ $group['title'] }}</h3>
                        <ul>
                            @foreach($group['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </x-section>

        <x-section tone="default" class="software-section" tag="Preguntas frecuentes" title="Respuestas para empezar">
            <div class="faq-list software-faqs">
                @foreach($service->faqs ?? [] as $faq)
                    <details class="faq-item glass-card">
                        <summary>{{ $faq['question'] }}</summary>
                        <p>{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </x-section>

        <x-section tone="contact" class="software-final" tag="Hablemos de tu proyecto" title="¿Tienes un proceso que podría funcionar mejor?" subtitle="Cuéntanos cómo trabaja actualmente tu empresa. Podemos analizar el proceso y evaluar qué solución tecnológica puede ayudarte a optimizarlo.">
            <div class="software-final-actions">
                <a href="{{ $contactUrl }}" class="btn btn-primary">Solicitar diagnóstico</a>
                <x-whatsapp-link from="service_cta" :message="$whatsappMessage" class="btn btn-outline-white" target="_blank">Hablar por WhatsApp</x-whatsapp-link>
            </div>
        </x-section>
    </article>
</x-app-layout>
