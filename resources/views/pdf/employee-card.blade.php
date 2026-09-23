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
            border: 0;
            box-shadow: none;
            font-family: Helvetica, Arial, sans-serif;
            page-break-after: always;
        }
        .employee-card--back { page-break-after: auto; }
        .employee-card__watermark { left: 29.5mm; top: .4mm; width: 45mm; height: 51.9mm; }
        .employee-card__watermark--back { left: 36.4mm; top: 1mm; width: 44mm; height: 50.75mm; }
        .employee-card__brand { left: 4.1mm; top: 4.25mm; width: 44.5mm; height: 11.9mm; }
        .employee-card__brand-mark { width: 9mm; height: 10.38mm; }
        .employee-card__brand-name,
        .employee-card__brand-company { left: 11.5mm; }
        .employee-card__brand-name { top: 1.65mm; font-size: 3.35mm; font-weight: bold; letter-spacing: .2mm; }
        .employee-card__brand-company { top: 7.1mm; font-size: 2mm; font-weight: bold; letter-spacing: .4mm; }
        .employee-card__portrait-window { top: 3mm; width: 45.8mm; height: 47mm; }
        .employee-card__copy { left: 4.1mm; top: 22.8mm; width: 46.2mm; }
        .employee-card__copy h1 { max-height: 6.2mm; overflow: hidden; font-size: 2.65mm; font-weight: bold; line-height: 1.08; letter-spacing: -.03mm; }
        .employee-card__position { max-width: 39mm; margin-top: 1.1mm; font-size: 2.3mm; font-weight: bold; }
        .employee-card__code { margin-top: 1mm; font-size: 2.18mm; }
        .employee-card__code strong { font-weight: bold; }
        .employee-card__authorization { margin-top: 2.5mm; padding: 1mm 1.9mm .9mm; font-size: 2.08mm; font-weight: bold; }
        .employee-card__portrait { filter: none; }
        .employee-card__back-copy { left: 4.65mm; top: 5.6mm; width: 53mm; }
        .employee-card__back-copy h2 { font-size: 2.7mm; font-weight: bold; }
        .employee-card__back-rule { width: 13.2mm; height: .26mm; margin-top: 1.35mm; }
        .employee-card__nit { margin-top: 1.9mm; font-size: 2.35mm; }
        .employee-card__notice { margin-top: 2.6mm; font-size: 1.88mm; line-height: 1.43; }
        .employee-card__contacts { margin-top: 1.3mm; }
        .employee-card__contacts p { margin-bottom: .6mm; font-size: 1.78mm; }
        .employee-card__contacts span { width: 2.8mm; height: 2.8mm; margin-right: 1mm; font-family: "DejaVu Sans", sans-serif; font-size: 1.5mm; line-height: 2.8mm; text-align: center; }
        .employee-card__signature-block { left: 4.65mm; bottom: 9mm; width: 42.8mm; height: 7.5mm; border-bottom-width: .16mm; }
        .employee-card__signature { left: 3mm; bottom: .5mm; width: 36.8mm; height: 6.5mm; }
        .employee-card__signature-block span { top: 8.15mm; font-size: 1.75mm; }
        .employee-card__qr-panel { right: 4.65mm; top: 12.7mm; width: 22.7mm; }
        .employee-card__qr-frame { width: 22.7mm; height: 22.7mm; border-width: .3mm; border-radius: 1.7mm; }
        .employee-card__qr { left: .55mm; top: .55mm; width: 21mm; height: 21mm; }
        .employee-card__qr-panel p { margin: 1mm -1.7mm 0; font-size: 1.58mm; }
        .employee-card__qr-pending { display: none; }
        .employee-card__footer { height: 3.9mm; }
        .employee-card__footer > span { width: 17mm; }
        .employee-card__footer--back p { font-size: 1.88mm; line-height: 3.9mm; }
    </style>
</head>
<body>
    <div class="employee-card-grid">
        @include('employees._card', ['card' => $card])
    </div>
</body>
</html>
