<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carnet {{ $employee->employee_code }}</title>
    @include('employees._card-styles')
    <style>
        body { margin: 0; padding: 2.5rem; background: linear-gradient(145deg, #eef3f5, #e6f5f2); }
        main { width: min(112rem, 100%); margin: 0 auto; }
        @media (max-width: 720px) { body { padding: 1rem; } }
    </style>
</head>
<body>
    <main class="employee-card-grid">
        @include('employees._card', ['card' => $card, 'showFaceLabels' => true])
    </main>
</body>
</html>
