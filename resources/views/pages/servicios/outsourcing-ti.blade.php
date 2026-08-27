<x-app-layout :title="'Outsourcing de TI – Smart Tech Security'">
    <article class="sd ot" data-wa-service="{{ $service->name }}">

        {{-- ═══ HERO ORIENTADO AL DOLOR ═══ --}}
        <header class="sd-hero">
            <div class="container">
                <nav class="sd-breadcrumb" aria-label="Ruta de navegación">
                    <a href="{{ route('home') }}">Inicio</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('servicios') }}">Servicios</a>
                    <span aria-hidden="true">/</span>
                    <span aria-current="page">Outsourcing de TI</span>
                </nav>

                <div class="sd-hero-grid">
                    <div class="sd-hero-copy">
                        <span class="sd-hero-kicker">
                            <x-service-icon :icon="$service->icon" :name="$service->name" size="sm" class="sd-hero-kicker-icon" />
                            Nuevo servicio para empresas
                        </span>
                        <h1 class="sd-hero-title">Deja de pagar un salario fijo de TI.</h1>
                        <p class="sd-hero-tagline">Ten un departamento de tecnología completo por una fracción del costo: soporte, redes, ciberseguridad y consultoría sin contratar personal de planta.</p>
                        <ul class="sd-hero-badges">
                            <li>Diagnóstico gratuito</li>
                            <li>SLA de respuesta por contrato</li>
                            <li>Sin permanencia ni cargas laborales</li>
                        </ul>
                        <div class="sd-hero-actions">
                            <a href="#diagnostico" class="btn btn-primary">Solicita tu diagnóstico gratuito</a>
                            <a href="#comparador" class="btn sd-btn-outline">Calcula tu ahorro</a>
                            <a href="https://wa.me/{{ config('contact.whatsapp') }}" class="btn sd-btn-outline" target="_blank" rel="noopener">WhatsApp</a>
                        </div>
                    </div>

                    <figure class="sd-hero-media">
                        <img src="{{ $service->image_url }}" alt="Outsourcing de TI para empresas" width="880" height="550" fetchpriority="high" />
                        @if($service->price_from)
                            <figcaption class="sd-hero-price">
                                Planes desde <strong>${{ number_format($service->price_from, 0, ',', '.') }}</strong> COP/mes
                            </figcaption>
                        @endif
                    </figure>
                </div>
            </div>
        </header>

        <div class="container ot-main">

            {{-- ═══ COMPARADOR DE COSTOS ═══ --}}
            <section class="sd-block" id="comparador" aria-labelledby="ot-comp">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Comparador</span>
                    <h2 class="sd-block-title" id="ot-comp">¿Cuánto te cuesta TI interno vs Smart Tech?</h2>
                </header>
                <livewire:it-cost-comparator />
            </section>

            {{-- ═══ CÓMO FUNCIONA (3 PASOS) ═══ --}}
            @if($service->process_steps)
                <section class="sd-block" aria-labelledby="ot-proc">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">Cómo funciona</span>
                        <h2 class="sd-block-title" id="ot-proc">Tu departamento de TI en 3 pasos</h2>
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

            {{-- ═══ PLANES FLEXIBLES ═══ --}}
            <section class="sd-block" id="planes" aria-labelledby="ot-plans">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Planes</span>
                    <h2 class="sd-block-title" id="ot-plans">Planes flexibles, ciberseguridad incluida en todos</h2>
                </header>
                <div class="ot-plans">
                    @foreach(config('quotes.it.plans') as $key => $plan)
                        <div class="ot-plan glass-card {{ $key === 'demanda' ? 'ot-plan--featured' : '' }}">
                            @if($key === 'demanda')
                                <span class="ot-plan-badge">Más elegido</span>
                            @endif
                            <h3 class="ot-plan-name">{{ $plan['name'] }}</h3>
                            <p class="ot-plan-tagline">{{ $plan['tagline'] }}</p>
                            <p class="ot-plan-price">
                                Desde <strong>${{ number_format($plan['price'], 0, ',', '.') }}</strong>
                                <span>COP/{{ $plan['unit'] }}</span>
                            </p>
                            <ul class="ot-plan-features">
                                @foreach($plan['features'] as $feature)
                                    <li><span class="sd-check" aria-hidden="true">✓</span> {{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="#diagnostico" class="btn {{ $key === 'demanda' ? 'btn-primary' : 'btn-ghost' }} ot-plan-cta">Empezar con diagnóstico gratis</a>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- ═══ QUÉ INCLUYE ═══ --}}
            @if($service->includes)
                <section class="sd-block" aria-labelledby="ot-inc">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">Servicios</span>
                        <h2 class="sd-block-title" id="ot-inc">Todo lo que cubre tu departamento de TI</h2>
                    </header>
                    <ul class="sd-includes">
                        @foreach($service->includes as $item)
                            <li class="sd-include">
                                <span class="sd-check" aria-hidden="true">✓</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ═══ TECNOLOGÍAS QUE DOMINAMOS ═══ --}}
            @if($service->brand_groups)
                <section class="sd-block" aria-labelledby="ot-tech">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">Tecnologías</span>
                        <h2 class="sd-block-title" id="ot-tech">Tecnologías que dominamos</h2>
                    </header>
                    @foreach($service->brand_groups as $group)
                        @if($group['label'])
                            <h3 class="sd-brand-group">{{ $group['label'] }}</h3>
                        @endif
                        <ul class="sd-brand-wall">
                            @foreach($group['brands'] as $brand)
                                <x-brand-logo :brand="$brand" />
                            @endforeach
                        </ul>
                    @endforeach
                </section>
            @endif

            {{-- ═══ CASOS POR PERFIL DE CLIENTE ═══ --}}
            <section class="sd-block" aria-labelledby="ot-cases">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Casos</span>
                    <h2 class="sd-block-title" id="ot-cases">¿Tu empresa se parece a alguna de estas?</h2>
                </header>
                <div class="ot-cases">
                    <div class="ot-case glass-card glass-card--pad">
                        <h3>Pyme sin área de TI</h3>
                        <p class="ot-case-before"><strong>Antes:</strong> cada falla detiene la operación; se llama "al que sabe" y se paga caro por soluciones improvisadas, sin copias de seguridad ni control.</p>
                        <p class="ot-case-after"><strong>Después:</strong> mesa de ayuda con SLA, equipos monitoreados, copias automáticas y un solo canal de soporte por menos de lo que cuesta medio salario.</p>
                    </div>
                    <div class="ot-case glass-card glass-card--pad">
                        <h3>Empresa multi-sede en expansión</h3>
                        <p class="ot-case-before"><strong>Antes:</strong> una persona de TI corre entre sedes, las redes crecen sin diseño y cada apertura de sede es una crisis.</p>
                        <p class="ot-case-after"><strong>Después:</strong> redes administradas de forma centralizada, visitas programadas por sede y despliegues de sedes nuevas con lista de chequeo probada.</p>
                    </div>
                    <div class="ot-case glass-card glass-card--pad">
                        <h3>Startup que necesita escalar</h3>
                        <p class="ot-case-before"><strong>Antes:</strong> el equipo fundador hace soporte, la seguridad es una deuda pendiente y contratar TI de planta se come el runway.</p>
                        <p class="ot-case-after"><strong>Después:</strong> ingeniero por demanda que crece con la operación, ciberseguridad desde el día uno y consultoría para decidir bien las compras de tecnología.</p>
                    </div>
                </div>
            </section>

            {{-- ═══ GARANTÍA SIN RIESGO ═══ --}}
            <section class="sd-block" aria-labelledby="ot-guar">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Sin riesgo</span>
                    <h2 class="sd-block-title" id="ot-guar">Garantía sin riesgo</h2>
                </header>
                <div class="ot-guarantee">
                    <div class="ot-guarantee-item glass-card glass-card--pad">
                        <h3>Periodo de prueba</h3>
                        <p>El primer mes es de prueba: si el servicio no te convence, no continúas. Sin permanencia ni cláusulas de salida.</p>
                    </div>
                    <div class="ot-guarantee-item glass-card glass-card--pad">
                        <h3>SLA con tiempos concretos</h3>
                        <p>Respuesta remota en menos de 2 horas hábiles y atención en sitio según tu plan, por contrato. No promesas: acuerdos.</p>
                    </div>
                    <div class="ot-guarantee-item glass-card glass-card--pad">
                        <h3>Satisfacción garantizada</h3>
                        <p>Informe mensual de gestión con tickets resueltos y tiempos reales. Si un mes no cumplimos el SLA, lo compensamos.</p>
                    </div>
                </div>
            </section>

            {{-- ═══ PRUEBA SOCIAL ═══ --}}
            <section class="sd-block" aria-labelledby="ot-proof">
                <header class="sd-block-head">
                    <span class="sd-block-tag">Confianza</span>
                    <h2 class="sd-block-title" id="ot-proof">Respaldo de un equipo con trayectoria</h2>
                </header>
                <div class="ot-metrics">
                    <div class="ot-metric">
                        <span class="ot-metric-value">10+</span>
                        <span class="ot-metric-label">años de experiencia en tecnología empresarial</span>
                    </div>
                    <div class="ot-metric">
                        <span class="ot-metric-value">&lt; 2 h</span>
                        <span class="ot-metric-label">tiempo de respuesta promedio en horario laboral</span>
                    </div>
                    <div class="ot-metric">
                        <span class="ot-metric-value">100%</span>
                        <span class="ot-metric-label">de los planes incluyen ciberseguridad, no es un extra</span>
                    </div>
                </div>
                <p class="ot-proof-more">
                    Conoce <a href="{{ route('proyectos') }}" class="service-link">los proyectos que hemos entregado →</a>
                </p>
            </section>

            {{-- ═══ FAQ ═══ --}}
            @if($service->faqs)
                <section class="sd-block" aria-labelledby="ot-faq">
                    <header class="sd-block-head">
                        <span class="sd-block-tag">FAQ</span>
                        <h2 class="sd-block-title" id="ot-faq">Preguntas frecuentes</h2>
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

            {{-- ═══ CTA FINAL + FORMULARIO DE DIAGNÓSTICO ═══ --}}
            <section class="sd-block ot-final" aria-labelledby="ot-cta">
                <div class="ot-final-grid">
                    <div class="ot-final-copy">
                        <span class="sd-block-tag">Empieza hoy</span>
                        <h2 class="sd-block-title" id="ot-cta">Tu diagnóstico gratuito en 3 datos</h2>
                        <p>Cuéntanos cómo está tu tecnología hoy y te entregamos un diagnóstico con riesgos, prioridades y el plan que más te conviene. Sin costo y sin compromiso.</p>
                        <ul class="sd-quote-trust">
                            <li>Respuesta en menos de 2 horas hábiles</li>
                            <li>Visita o videollamada de diagnóstico gratuita</li>
                            <li>Propuesta clara con precios desde, en COP</li>
                        </ul>
                    </div>
                    <livewire:it-diagnostic-form />
                </div>
            </section>

        </div>
    </article>
</x-app-layout>
