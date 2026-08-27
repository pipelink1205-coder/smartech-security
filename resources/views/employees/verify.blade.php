<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Verificación de empleado | Smart Tech Security</title>
    <style>
        :root {
            color-scheme: light;
            --teal: #178f82;
            --teal-dark: #0d6b61;
            --ink: #0c2332;
            --muted: #4b5d66;
            --line: #d7e4e1;
            --pale: #eef7f5;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            padding: 1.25rem;
            background: #f3f7f6;
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }
        .verification-shell { width: min(26rem, 100%); margin: 0 auto; }
        .verification-card {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 1.15rem;
            background: #fff;
            box-shadow: 0 .75rem 2rem rgba(12, 35, 50, .08);
        }
        .verification-header {
            padding: 1.25rem 1.25rem 0;
            text-align: center;
        }
        .brand-lockup { display: flex; gap: .7rem; align-items: center; justify-content: center; }
        .brand-logo { width: 3.4rem; height: 3.4rem; object-fit: contain; }
        .brand-copy { color: var(--ink); text-align: left; }
        .brand-copy strong { display: block; font-size: 1.05rem; line-height: 1.05; letter-spacing: .02em; }
        .brand-copy span { display: block; margin-top: .2rem; color: var(--teal-dark); font-size: .68rem; font-weight: 800; letter-spacing: .18em; }
        .status-badge {
            display: inline-flex;
            margin-top: 1rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            color: #fff;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
        }
        .status-badge--active { background: var(--teal); }
        .status-badge--inactive { background: #b42318; }
        .employee-photo-wrap {
            width: 9.5rem;
            height: 11.4rem;
            margin: 1.15rem auto .9rem;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: .7rem;
            background: var(--pale);
        }
        .employee-photo { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center 18%; }
        .employee-photo-empty { display: grid; height: 100%; place-items: center; color: #7b8a92; font-size: .85rem; font-weight: 700; }
        .employee-copy { padding: 0 1.25rem; text-align: center; }
        h1 { margin: 0; font-size: clamp(1.15rem, 4.6vw, 1.45rem); line-height: 1.15; }
        .position { margin: .4rem 0 .2rem; color: var(--teal-dark); font-size: .98rem; font-weight: 650; }
        .code { margin: 0; color: var(--muted); font-size: .92rem; }
        .code strong { color: var(--ink); }
        .company-panel {
            margin: 1.1rem 1.25rem 0;
            padding: .85rem 1rem;
            border-top: 1px solid var(--line);
            text-align: center;
        }
        .company-panel span { color: var(--muted); font-size: .72rem; font-weight: 750; letter-spacing: .08em; text-transform: uppercase; }
        .company-panel strong { display: block; margin-top: .25rem; }
        .company-panel p { margin: .15rem 0 0; color: var(--muted); }
        .security-note {
            margin: 0 1.25rem;
            padding: 0 0 1.1rem;
            color: var(--muted);
            text-align: center;
            font-size: .82rem;
            line-height: 1.45;
        }
        .verification-actions { padding: 0 1.25rem 1.15rem; text-align: center; }
        .button {
            display: inline-flex;
            min-height: 2.6rem;
            align-items: center;
            justify-content: center;
            padding: 0 1rem;
            border: 1px solid var(--teal);
            border-radius: .6rem;
            color: var(--teal-dark);
            font-size: .88rem;
            font-weight: 750;
            text-decoration: none;
        }
        footer { padding: .7rem 1rem; background: var(--ink); color: #fff; text-align: center; font-size: .8rem; }
        @media (max-width: 480px) { body { padding: .65rem; } }
    </style>
</head>
<body>
    @php($isActive = $employee->status === 'active')
    <main class="verification-shell">
        <article class="verification-card">
            <header class="verification-header">
                <div class="brand-lockup">
                    <img class="brand-logo" src="{{ asset('images/logo-transparent.png') }}" alt="">
                    <div class="brand-copy">
                        <strong>SMART TECH</strong>
                        <span>SECURITY S.A.S.</span>
                    </div>
                </div>
                <div class="status-badge {{ $isActive ? 'status-badge--active' : 'status-badge--inactive' }}">
                    {{ $isActive ? '✓ EMPLEADO VERIFICADO' : 'CREDENCIAL NO VIGENTE' }}
                </div>
            </header>

            <div class="employee-photo-wrap">
                @if($employee->public_photo_data_uri)
                    <img class="employee-photo" src="{{ $employee->public_photo_data_uri }}" alt="Fotografía de {{ $employee->full_name }}">
                @else
                    <div class="employee-photo-empty">Fotografía no disponible</div>
                @endif
            </div>

            <section class="employee-copy">
                <h1>{{ mb_strtoupper($employee->full_name) }}</h1>
                <p class="position">{{ $employee->position }}</p>
                <p class="code">Código <strong>{{ $employee->employee_code }}</strong></p>
            </section>

            <section class="company-panel">
                <span>Empresa</span>
                <strong>SMART TECH SECURITY S.A.S.</strong>
                <p>NIT 901.124.137-1</p>
            </section>

            <p class="security-note">
                {{ $isActive
                    ? 'Esta persona trabaja en Smart Tech Security. La cédula no se muestra por seguridad.'
                    : 'Esta persona no tiene una credencial activa. Confirma cualquier visita con la empresa.' }}
            </p>

            <nav class="verification-actions" aria-label="Contacto de Smart Tech Security">
                <a class="button" href="{{ route('contacto') }}">Contactar a Smart Tech Security</a>
            </nav>

            <footer>smarttechsecurity.com.co</footer>
        </article>
    </main>
</body>
</html>
