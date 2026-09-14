<x-app-layout :title="'Software, web y analítica a la medida – Smart Tech Security'">
    <article class="sd sa" data-wa-service="{{ $service->name }}">

        <header class="sd-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('servicios') }}">Servicios</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Software y analítica</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker">
                            <x-service-icon :icon="$service->icon" :name="$service->name" size="sm" class="sd-hero-kicker-icon" />
                            Nuevo servicio · Desarrollo y datos
                        </span>
                        <h1 class="sd-hero-title">No te vendemos un sistema. Te construimos el tablero con el que decides.</h1>
                        <p class="sd-hero-tagline">Páginas web que venden, software a la medida de tu operación y analítica para dejar de adivinar. Del primer menú a la cifra que el dueño necesita el lunes.</p>
                        <ul class="sd-hero-badges">
                            <li>Prototipo visible antes de programar</li>
                            <li>Entregas semanales, no a los tres meses</li>
                            <li>Diagnóstico digital gratuito</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="#modelos" class="btn btn-primary">Ver modelos</a>
                            <a href="{{ route('contacto') }}" class="btn sd-btn-outline">Pedir diagnóstico</a>
                            <x-whatsapp-link from="service_hero" class="btn sd-btn-outline" target="_blank">WhatsApp</x-whatsapp-link>
                        </div>
                    </div>

                    <figure class="sd-hero-media">
                        <img src="{{ $service->image_url }}" alt="Sitio web y tablero de analítica en el mismo escritorio" width="880" height="550" fetchpriority="high" />
                        @if($service->price_from)
                            <figcaption class="sd-hero-price">
                                Proyectos desde <strong>${{ number_format($service->price_from, 0, ',', '.') }}</strong> COP
                            </figcaption>
                        @endif
                    </figure>
                </div>
            </div>
        </header>

        <div class="container sa-main">

            <section class="sd-block" aria-labelledby="sa-pillars">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Tres capas</span>
                    <h2 class="sd-block-title" id="sa-pillars">Así se ve un negocio que ya no depende del Excel</h2>
                </header>
                <div class="sa-pillars">
                    <article class="sa-pillar glass-card glass-card--pad">
                        <p class="sa-pillar-kicker">01 · Presencia</p>
                        <h3>Web y menús que venden</h3>
                        <p>Sitios con navegación clara, móvil impecable y formularios que sí llegan. Hoteles, clínicas, comercios y empresas de servicios que hoy se ven amateur frente a la competencia.</p>
                    </article>
                    <article class="sa-pillar glass-card glass-card--pad">
                        <p class="sa-pillar-kicker">02 · Operación</p>
                        <h3>Software a tu proceso</h3>
                        <p>Órdenes de trabajo, visitas técnicas, inventario, clientes o carnets. Lo que hoy vive en WhatsApp y libretas, en un panel con roles y seguimiento.</p>
                    </article>
                    <article class="sa-pillar glass-card glass-card--pad sa-pillar--featured">
                        <p class="sa-pillar-kicker">03 · Decisión</p>
                        <h3>Analítica que el dueño entiende</h3>
                        <p>Dashboards, alertas y tendencias. No un reporte de 40 páginas: las 6 cifras que importan, actualizadas. Analistas de datos + desarrolladores en el mismo equipo.</p>
                    </article>
                </div>
            </section>

            <section class="sd-block" id="modelos" aria-labelledby="sa-models">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Modelos</span>
                    <h2 class="sd-block-title" id="sa-models">Así se ve el trabajo: menús, paneles y tableros</h2>
                    <p class="sa-lead">Ejemplos de estilo. Cada proyecto se diseña con tu marca, tus datos y tu forma de trabajar.</p>
                </header>
                <div class="sa-models">
                    <figure class="sa-model">
                        <img src="{{ asset('images/services/software-analitica-web.png') }}" alt="Modelo de sitio web con menú Inicio, Habitaciones, Experiencias y Contacto" width="960" height="540" />
                        <figcaption>
                            <strong>Sitio web</strong>
                            Menú limpio, hero y tarjetas. Pensado para que el visitante reserve o escriba, no para “llenar espacio”.
                        </figcaption>
                    </figure>
                    <figure class="sa-model">
                        <img src="{{ asset('images/services/software-analitica-app.png') }}" alt="Modelo de software operativo con menú lateral y órdenes de trabajo" width="960" height="540" />
                        <figcaption>
                            <strong>Software operativo</strong>
                            Sidebar, estados y equipo. El supervisor ve qué está abierto, en ruta o cerrado sin preguntar por chat.
                        </figcaption>
                    </figure>
                    <figure class="sa-model sa-model--wide">
                        <img src="{{ asset('images/services/software-analitica-dashboard.png') }}" alt="Modelo de dashboard analítico con indicadores, gráficos y mapa" width="960" height="540" />
                        <figcaption>
                            <strong>Dashboard de analítica</strong>
                            Ventas, visitas, alertas y ahorro en una sola pantalla. Para gerencia, no para el área de sistemas.
                        </figcaption>
                    </figure>
                </div>
            </section>

            <section class="sd-block" aria-labelledby="sa-types">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Alcance</span>
                    <h2 class="sd-block-title" id="sa-types">Qué desarrollamos (y qué no es “solo una página”)</h2>
                </header>
                <ul class="sa-types">
                    <li><strong>Páginas web y landings</strong> Catálogo, reservas, contacto y SEO local. Como este sitio: rápido en celular.</li>
                    <li><strong>Software a la medida</strong> Flujos de tu negocio: cotizaciones, visitas, inventario, clientes, carnets.</li>
                    <li><strong>Analítica y tableros</strong> Indicadores, comparativos, alertas y lectura de Excel o bases que ya tienes.</li>
                    <li><strong>Integraciones</strong> WhatsApp, correo, pasarelas, DIAN, APIs y, si aplica, la operación que ya instalamos (acceso, energía, campo).</li>
                    <li><strong>Mejora de procesos</strong> Antes de codear, recortamos pasos innecesarios. El software no debe digitalizar el desorden.</li>
                    <li><strong>Acompañamiento</strong> Capacitación, ajustes por sprints y un admin para que no pagues un programador por cada texto.</li>
                </ul>
            </section>

            @if($service->process_steps)
                <section class="sd-block" aria-labelledby="sa-proc">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">Método</span>
                        <h2 class="sd-block-title" id="sa-proc">De la conversación al tablero, en 4 pasos</h2>
                    </header>
                    <ol class="sd-steps">
                        @foreach($service->process_steps as $index => $step)
                            <li class="sd-step">
                                <span class="sd-step-num" aria-hidden="true">{{ $index + 1 }}</span>
                                <p class="sd-step-text">{{ $step }}</p>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif

            <section class="sd-block" aria-labelledby="sa-who">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Para quién</span>
                    <h2 class="sd-block-title" id="sa-who">Dueños que ya operan, y les falta visibilidad</h2>
                </header>
                <div class="sa-who">
                    <p>Empresas de servicios, comercio, salud, hotelería y operación de campo en Medellín y el Valle de Aburrá. Si tu equipo pregunta “¿cómo vamos?” y la respuesta tarda un día, este servicio es para ti.</p>
                    <p>Si ya trabajas con Smart Tech en seguridad, energía o redes, el siguiente paso natural es <em>ver</em> esa operación: no solo instalarla.</p>
                </div>
            </section>

            @if($service->faqs)
                <section class="sd-block" aria-labelledby="sa-faq">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">FAQ</span>
                        <h2 class="sd-block-title" id="sa-faq">Preguntas frecuentes</h2>
                    </header>
                    <div class="sd-faqs">
                        @foreach($service->faqs as $faq)
                            <details class="sd-faq glass-card glass-card--pad">
                                <summary>{{ $faq['question'] ?? '' }}</summary>
                                <p>{{ $faq['answer'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="sd-block sa-final" aria-labelledby="sa-cta">
                <div class="sa-final-inner glass-card glass-card--pad">
                    <span class="sd-block-tag">Empieza</span>
                    <h2 class="sd-block-title" id="sa-cta">Diagnóstico digital en una conversación</h2>
                    <p>Cuéntanos si el dolor es la web, el Excel o la falta de cifras. Te devolvemos un mapa corto: qué construir primero, en cuánto y para qué decisión.</p>
                    <div class="sd-hero-actions">
                        <a href="{{ route('contacto') }}" class="btn btn-primary">Solicitar diagnóstico</a>
                        <x-whatsapp-link from="service_cta" class="btn sd-btn-outline" target="_blank">WhatsApp</x-whatsapp-link>
                    </div>
                </div>
            </section>

        </div>
    </article>
</x-app-layout>
