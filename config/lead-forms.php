<?php

/*
|--------------------------------------------------------------------------
| Formularios de contacto por servicio (contextuales)
|--------------------------------------------------------------------------
| Cada página /servicios/{slug} muestra su propio formulario con el servicio
| ya fijado y preguntas propias. Las respuestas llegan al lead (Cotizaciones).
| - title / subtitle: encabezado del formulario
| - whatsapp: mensaje precargado del botón de WhatsApp de esa página
| - placeholder: ejemplo en el campo de mensaje
| - questions: clave => [label, options[]] (se muestran como botones)
| Si un servicio no está aquí, usa 'default'.
*/

return [

    'default' => [
        'title' => 'Cuéntenos qué necesita',
        'subtitle' => 'Respondemos en menos de 2 horas hábiles. La visita técnica es gratuita.',
        'whatsapp' => 'Hola, quiero información sobre :service. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: lo que necesita y dónde',
        'questions' => [
            'lugar' => ['label' => '¿Para qué tipo de lugar?', 'options' => ['Casa o apartamento', 'Negocio u oficina', 'Unidad residencial', 'Hotel', 'Industria o bodega']],
        ],
    ],

    'camaras-4k' => [
        'title' => 'Cotice sus cámaras de seguridad',
        'subtitle' => 'Con dos datos le damos un estimado y agendamos la visita técnica gratuita.',
        'whatsapp' => 'Hola, quiero cotizar cámaras de seguridad. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: quiero vigilar la entrada, el parqueadero y ver todo desde el celular',
        'questions' => [
            'lugar' => ['label' => '¿Dónde las instalaría?', 'options' => ['Casa o apartamento', 'Negocio u oficina', 'Unidad residencial', 'Industria o bodega']],
            'cantidad' => ['label' => '¿Cuántas cámaras, aproximadamente?', 'options' => ['1 a 4', '5 a 8', '9 a 16', 'Más de 16', 'No sé, necesito asesoría']],
        ],
    ],

    'energia-solar' => [
        'title' => 'Calcule su sistema de energía solar',
        'subtitle' => 'Con su consumo y tipo de techo dimensionamos el sistema en la visita técnica gratuita.',
        'whatsapp' => 'Hola, quiero saber cuánto puedo ahorrar con energía solar. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: casa de dos pisos, techo con buena luz en la tarde',
        'questions' => [
            'factura' => ['label' => '¿Cuánto paga de energía al mes?', 'options' => ['Menos de $200.000', '$200.000 a $500.000', '$500.000 a $1.500.000', 'Más de $1.500.000']],
            'techo' => ['label' => '¿Qué tipo de techo tiene?', 'options' => ['Teja de barro', 'Teja de fibrocemento o metálica', 'Losa de concreto', 'No estoy seguro']],
        ],
    ],

    'control-acceso' => [
        'title' => 'Cotice su control de acceso',
        'subtitle' => 'Biometría, tarjetas, tags o QR. Le recomendamos la mejor opción para su flujo de personas.',
        'whatsapp' => 'Hola, quiero cotizar un control de acceso. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: controlar la entrada de empleados y registrar horarios',
        'questions' => [
            'lugar' => ['label' => '¿Dónde lo necesita?', 'options' => ['Oficina o empresa', 'Unidad residencial', 'Local comercial', 'Industria o bodega']],
            'accesos' => ['label' => '¿Cuántos puntos de acceso?', 'options' => ['1', '2 a 3', '4 o más']],
        ],
    ],

    'control-acceso-vehicular' => [
        'title' => 'Cotice su control de acceso vehicular',
        'subtitle' => 'Talanqueras, tags UHF o lectura de placas, según el flujo de vehículos.',
        'whatsapp' => 'Hola, quiero cotizar un control de acceso vehicular. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: portería con una entrada y una salida, unos 80 vehículos',
        'questions' => [
            'lugar' => ['label' => '¿Dónde lo necesita?', 'options' => ['Unidad residencial', 'Empresa o parqueadero', 'Industria o bodega']],
            'carriles' => ['label' => '¿Cuántos carriles de entrada/salida?', 'options' => ['1', '2', '3 o más']],
        ],
    ],

    'video-porteros-citofonia-ip' => [
        'title' => 'Cotice su video portero o citofonía IP',
        'subtitle' => 'Desde un video portero para casa hasta citofonía IP para una unidad completa.',
        'whatsapp' => 'Hola, quiero cotizar video portero o citofonía IP. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: cambiar la citofonía análoga de dos torres',
        'questions' => [
            'lugar' => ['label' => '¿Para dónde es?', 'options' => ['Casa', 'Edificio pequeño', 'Unidad residencial', 'Oficina']],
            'unidades' => ['label' => '¿Cuántos apartamentos u oficinas?', 'options' => ['1', '2 a 20', '21 a 100', 'Más de 100']],
        ],
    ],

    'alarmas' => [
        'title' => 'Cotice su sistema de alarma',
        'subtitle' => 'Sensores, sirenas y avisos al celular, configurados para su espacio.',
        'whatsapp' => 'Hola, quiero cotizar un sistema de alarma. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: alarma para el local, con aviso al celular cuando se abra la puerta',
        'questions' => [
            'lugar' => ['label' => '¿Dónde la instalaría?', 'options' => ['Casa o apartamento', 'Local comercial', 'Oficina', 'Bodega']],
            'actual' => ['label' => '¿Ya tiene alguna alarma?', 'options' => ['No', 'Sí, pero no funciona bien', 'Sí, quiero ampliarla']],
        ],
    ],

    'deteccion-incendios' => [
        'title' => 'Cotice su sistema de detección de incendios',
        'subtitle' => 'Detectores, panel y alarmas para cumplir requisitos y proteger a las personas.',
        'whatsapp' => 'Hola, quiero cotizar un sistema de detección de incendios. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: bodega de 400 m² que necesita detección para cumplir una inspección',
        'questions' => [
            'lugar' => ['label' => '¿Para qué tipo de lugar?', 'options' => ['Oficina', 'Bodega o industria', 'Hotel', 'Unidad residencial', 'Local comercial']],
            'motivo' => ['label' => '¿Por qué lo necesita?', 'options' => ['Requisito de bomberos o inspección', 'Instalación nueva', 'Revisar un sistema existente']],
        ],
    ],

    'domotica' => [
        'title' => 'Diseñemos su casa inteligente',
        'subtitle' => 'Iluminación, cortinas, sonido y seguridad controlados desde el celular o por voz.',
        'whatsapp' => 'Hola, quiero automatizar mi casa. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: controlar luces y cortinas de la sala y las habitaciones',
        'questions' => [
            'etapa' => ['label' => '¿En qué etapa está su vivienda?', 'options' => ['Ya la habito', 'En obra o remodelación', 'Apartamento sobre planos']],
            'interes' => ['label' => '¿Qué le interesa automatizar primero?', 'options' => ['Iluminación', 'Cortinas', 'Seguridad', 'Sonido o cine en casa', 'Todo']],
        ],
    ],

    'redes-fibra' => [
        'title' => 'Cotice su red o fibra óptica',
        'subtitle' => 'Cableado estructurado, fibra y WiFi empresarial con certificación.',
        'whatsapp' => 'Hola, quiero cotizar una red o fibra óptica. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: oficina nueva de 25 puestos con WiFi para visitantes',
        'questions' => [
            'lugar' => ['label' => '¿Dónde es el proyecto?', 'options' => ['Oficina', 'Empresa con varias sedes', 'Hotel', 'Unidad residencial', 'Industria o bodega']],
            'puntos' => ['label' => '¿Cuántos puntos de red, aproximadamente?', 'options' => ['Menos de 10', '10 a 50', 'Más de 50', 'No sé']],
        ],
    ],

    'enlaces-inalambricos' => [
        'title' => 'Cotice su enlace inalámbrico',
        'subtitle' => 'Conectamos sedes, bodegas o porterías sin cablear entre ellas.',
        'whatsapp' => 'Hola, quiero cotizar un enlace inalámbrico. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: llevar internet de la oficina a una bodega a 300 m',
        'questions' => [
            'distancia' => ['label' => '¿A qué distancia están los puntos?', 'options' => ['Menos de 500 m', '500 m a 2 km', 'Más de 2 km', 'No sé']],
            'vista' => ['label' => '¿Hay línea de vista entre ellos?', 'options' => ['Sí', 'No', 'No estoy seguro']],
        ],
    ],

    'iptv-hoteles' => [
        'title' => 'Cotice IPTV para su hotel',
        'subtitle' => 'Canales HD, video bajo demanda e integración con su PMS. Le preparamos una demo.',
        'whatsapp' => 'Hola, quiero información de IPTV para mi hotel. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: hotel boutique en El Poblado, queremos menú de bienvenida con el logo',
        'questions' => [
            'habitaciones' => ['label' => '¿Cuántas habitaciones tiene el hotel?', 'options' => ['Menos de 20', '20 a 50', '51 a 150', 'Más de 150']],
            'tvs' => ['label' => '¿Qué televisores tiene hoy?', 'options' => ['Smart TV', 'Televisores sin smart', 'Mezcla de ambos', 'Vamos a comprar nuevos']],
        ],
    ],

    'ciberseguridad-empresas' => [
        'title' => 'Evaluemos la seguridad de su empresa',
        'subtitle' => 'Firewall, antivirus empresarial, copias de seguridad y buenas prácticas.',
        'whatsapp' => 'Hola, quiero mejorar la ciberseguridad de mi empresa. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: tuvimos un intento de phishing y no tenemos copias de seguridad',
        'questions' => [
            'equipos' => ['label' => '¿Cuántos equipos tiene la empresa?', 'options' => ['1 a 10', '11 a 50', 'Más de 50']],
            'prioridad' => ['label' => '¿Qué le preocupa más?', 'options' => ['Virus o ransomware', 'Copias de seguridad', 'Red y firewall', 'Correo y phishing', 'Una revisión general']],
        ],
    ],

    'desarrollo-software' => [
        'title' => 'Cuéntenos su proyecto de software',
        'subtitle' => 'Le respondemos con una propuesta de alcance, tiempos y forma de trabajo.',
        'whatsapp' => 'Hola, tengo un proyecto de software y quiero conversarlo. ¿Me pueden orientar?',
        'placeholder' => 'Ej.: automatizar las cotizaciones que hoy hacemos en Excel',
        'questions' => [
            'tipo' => ['label' => '¿Qué necesita?', 'options' => ['Software a la medida', 'Aplicación web', 'Automatizar un proceso', 'Analítica de datos', 'Inteligencia artificial']],
            'etapa' => ['label' => '¿En qué etapa está?', 'options' => ['Solo la idea', 'Tengo requisitos definidos', 'Mejorar un sistema existente']],
        ],
    ],
];
