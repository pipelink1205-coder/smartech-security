<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    @include('employees._card-styles')
    <style>
        @page { margin: 0; size: 85.6mm 54mm; }
        body { margin: 0; padding: 0; }
        .employee-card-grid { display: block; }
        .employee-card {
            width: 85.6mm;
            height: 54mm;
            border-radius: 0;
            box-shadow: none;
            font-family: "DejaVu Sans", sans-serif;
            page-break-after: always;
        }
        .employee-card--back { page-break-after: auto; }
        .employee-card__copy { left: 4.1mm; top: 19.55mm; width: 42mm; }
        .employee-card__copy h1 { height: 6.8mm; overflow: hidden; font-family: "DejaVu Sans", sans-serif; font-size: 2.35mm; font-weight: 700; line-height: 1.15; letter-spacing: -.02mm; }
        .employee-card__position { height: 5.8mm; padding-top: .68mm; font-size: 2.44mm; }
        .employee-card__code { margin-top: .38mm; font-size: 2.27mm; }
        .employee-card__portrait { filter: none; }
        .employee-card__signature { left: 6.25mm; bottom: 5.02mm; width: 40.66mm; height: 8.37mm; }
        .employee-card__qr { right: 7.32mm; bottom: 2.08mm; width: 15.75mm; }
        .employee-card__qr-pending { display: none; }
    </style>
</head>
<body>
    <div class="employee-card-grid">
        @include('employees._card', ['card' => $card])
    </div>
</body>
</html>
