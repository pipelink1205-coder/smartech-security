<?php

/*
|--------------------------------------------------------------------------
| Nicho: Propiedad Horizontal (unidades / conjuntos residenciales)
|--------------------------------------------------------------------------
| Contenido de las landings SEO /unidades-residenciales/...
| - servicios: 4 servicios pensados para administradores y consejos.
| - municipios: contexto local de cada municipio (primera ola).
| - enfoque: frase específica por servicio × municipio (evita páginas duplicadas).
| Edita los textos aquí; rutas, sitemap y schema se generan solos.
*/

return [

    'hub' => [
        'title' => 'Seguridad para unidades y conjuntos residenciales en el Valle de Aburrá | Smart Tech Security',
        'description' => 'Cámaras IP, control de acceso, citofonía IP y mantenimiento para unidades y conjuntos residenciales en Envigado, Sabaneta, Medellín, Itagüí, La Estrella y Bello. Diagnóstico gratuito.',
    ],

    'diagnostico' => [
        'title' => 'Diagnóstico de seguridad gratuito para unidades residenciales | Smart Tech Security',
        'description' => 'Recorrido técnico de 45 minutos e informe PDF con puntos ciegos, estado de equipos y tres opciones de mejora para presentar al consejo de administración. Sin costo.',
    ],

    'dolores' => [
        ['icon' => 'parking', 'title' => 'Robos en parqueaderos', 'text' => 'Bicicletas, motos y paquetes que desaparecen sin una grabación útil para reclamar.'],
        ['icon' => 'camera-off', 'title' => 'Cámaras que no sirven', 'text' => 'Equipos analógicos viejos, discos llenos o imágenes donde no se reconoce un rostro ni una placa.'],
        ['icon' => 'bell', 'title' => 'Portería saturada', 'text' => 'Domicilios, visitantes y residentes al tiempo, con citófonos dañados y llamadas que nadie contesta.'],
        ['icon' => 'key', 'title' => 'Accesos sin control', 'text' => 'Tarjetas clonadas o prestadas, sin registro de quién entró ni a qué hora.'],
    ],

    'paquetes' => [
        'esencial' => [
            'name' => 'Esencial',
            'tagline' => 'Unidades pequeñas o con cámaras viejas',
            'features' => [
                'Renovación de CCTV analógico a cámaras IP',
                'Visualización en celular para portería y administración',
                'Grabación continua de 30 días o más',
                'Capacitación al personal de portería',
            ],
        ],
        'acceso' => [
            'name' => 'Acceso Seguro',
            'tagline' => 'Unidades medianas con problemas de ingreso',
            'featured' => true,
            'features' => [
                'Todo lo del paquete Esencial',
                'Acceso peatonal biométrico, con tag o QR',
                'Control vehicular con tag o lectura de placas',
                'QR temporal para visitantes y domicilios',
            ],
        ],
        'inteligente' => [
            'name' => 'Unidad Inteligente',
            'tagline' => 'Unidades grandes y proyectos nuevos',
            'features' => [
                'Todo lo del paquete Acceso Seguro',
                'Citofonía IP con llamada a la app del residente',
                'Red y fibra para zonas comunes',
                'Panel único para portería y administración',
            ],
        ],
    ],

    'mantenimiento_plus' => [
        'Visita preventiva mensual',
        'Soporte 24/7 con tiempo de respuesta por contrato',
        'Revisión de grabaciones y salud de discos',
        'Informe mensual para el consejo',
    ],

    'proceso' => [
        ['title' => 'Diagnóstico gratuito', 'text' => 'Recorrido de 45 minutos por portería, parqueaderos, perímetro y cuarto de equipos.'],
        ['title' => 'Informe para el consejo', 'text' => 'PDF con fotos, puntos ciegos y tres opciones de mejora, listo para presentar.'],
        ['title' => 'Propuesta por paquete', 'text' => 'Valor cerrado y opción de pago en cuotas, sin depender de una cuota extraordinaria.'],
        ['title' => 'Instalación por etapas', 'text' => 'Sin dejar la portería sin servicio, con capacitación y garantía escrita.'],
    ],

    'faqs_generales' => [
        ['question' => '¿El diagnóstico de seguridad tiene algún costo?', 'answer' => 'No. El recorrido y el informe en PDF son gratuitos y sin compromiso. El administrador puede usarlo incluso para pedir otras cotizaciones.'],
        ['question' => '¿Pueden presentar la propuesta ante el consejo o la asamblea?', 'answer' => 'Sí. Preparamos una presentación corta con el diagnóstico, las opciones y el valor de cada una, y podemos acompañar al administrador en la reunión.'],
        ['question' => '¿Se puede pagar por cuotas?', 'answer' => 'Sí. Los paquetes se pueden dividir en cuotas para que la inversión quepa en el presupuesto ordinario de la unidad.'],
        ['question' => '¿Qué pasa con las grabaciones y los datos de los residentes?', 'answer' => 'Configuramos los equipos de acuerdo con la Ley 1581 de 2012: usuarios por rol, tiempos de retención definidos y avisos de videovigilancia en las zonas comunes.'],
    ],

    'servicios' => [
        'camaras-de-seguridad' => [
            'name' => 'Cámaras de seguridad',
            'short' => 'cámaras de seguridad',
            'icon' => 'camera',
            'image_service' => 'camaras-4k',
            'related_slugs' => ['camaras-4k'],
            'h1' => 'Cámaras de seguridad para unidades residenciales',
            'tagline' => 'CCTV IP que graba lo que importa: porterías, parqueaderos, shut de basuras y perímetro, con acceso desde el celular para la administración.',
            'intro' => 'Muchas unidades y conjuntos del Valle de Aburrá todavía tienen cámaras analógicas instaladas hace más de diez años. Graban, pero no permiten reconocer un rostro ni una placa cuando más se necesita. Renovamos el sistema por etapas, reutilizando cableado cuando es posible, para que el consejo vea resultados sin una inversión de golpe.',
            'includes' => [
                'Levantamiento de puntos ciegos en parqueaderos, porterías y perímetro',
                'Cámaras IP con visión nocturna y lentes para reconocer placas',
                'Grabador NVR con disco para 30 días o más',
                'Visualización en celular para administración y portería',
                'Avisos de videovigilancia según la Ley 1581',
                'Capacitación al personal de vigilancia',
            ],
            'paquete' => 'esencial',
            'faqs' => [
                ['question' => '¿Se puede aprovechar el cableado de las cámaras actuales?', 'answer' => 'En muchos casos sí. Con conversores o cámaras compatibles se reutiliza parte del cableado coaxial, lo que reduce costos y tiempos de obra.'],
                ['question' => '¿Cuántos días de grabación quedan guardados?', 'answer' => 'Dimensionamos el disco para un mínimo de 30 días continuos. Si la unidad necesita más, se amplía el almacenamiento.'],
                ['question' => '¿La administración puede ver las cámaras desde el celular?', 'answer' => 'Sí, con usuarios por rol: la administración ve todo, la portería ve su zona y nadie más tiene acceso sin autorización.'],
            ],
        ],
        'control-de-acceso' => [
            'name' => 'Control de acceso',
            'short' => 'control de acceso',
            'icon' => 'fingerprint',
            'image_service' => 'control-acceso',
            'related_slugs' => ['control-acceso', 'control-acceso-vehicular'],
            'h1' => 'Control de acceso para unidades residenciales',
            'tagline' => 'Ingreso peatonal y vehicular con biometría, tags o QR para visitantes. Cada entrada queda registrada y la portería deja de depender de la memoria del guarda.',
            'intro' => 'Las tarjetas de proximidad antiguas se clonan en minutos y se prestan sin control. Un sistema de acceso moderno registra quién entra, por dónde y a qué hora, y permite dar de baja un tag perdido desde la administración en segundos.',
            'includes' => [
                'Lectores biométricos, de tag o de QR en accesos peatonales',
                'Control vehicular con tag UHF o lectura de placas',
                'QR temporal para visitantes y domiciliarios',
                'Software de administración con reportes de ingresos',
                'Integración con talanqueras y puertas existentes',
                'Carnetización y entrega de tags a residentes',
            ],
            'paquete' => 'acceso',
            'faqs' => [
                ['question' => '¿Qué es mejor para una unidad residencial: huella, tag o QR?', 'answer' => 'Depende del flujo. La huella o el rostro evitan préstamos en accesos peatonales, el tag UHF agiliza vehículos y el QR temporal es ideal para visitantes. Lo usual es combinarlos.'],
                ['question' => '¿Qué pasa si se va la luz?', 'answer' => 'Los equipos quedan con respaldo de energía (UPS) y las puertas con apertura de emergencia, para no comprometer la evacuación.'],
                ['question' => '¿Se pueden reutilizar las talanqueras actuales?', 'answer' => 'Normalmente sí. Integramos los nuevos lectores a talanqueras y cerraduras que estén en buen estado.'],
            ],
        ],
        'citofonia-ip' => [
            'name' => 'Citofonía IP',
            'short' => 'citofonía IP',
            'icon' => 'phone',
            'image_service' => 'video-porteros-citofonia-ip',
            'related_slugs' => ['video-porteros-citofonia-ip'],
            'h1' => 'Citofonía IP para unidades residenciales',
            'tagline' => 'La portería llama directo a la app del residente, con video. Se acaban los citófonos dañados y las filas de domiciliarios esperando respuesta.',
            'intro' => 'La citofonía análoga de muchas unidades ya no tiene repuestos y cada daño deja apartamentos incomunicados. Con citofonía IP, el residente recibe la llamada en el celular aunque no esté en casa, y la portería atiende domicilios y visitas mucho más rápido.',
            'includes' => [
                'Consola de portería con video',
                'Llamada a la app del residente, dentro y fuera de casa',
                'Monitores interiores opcionales por apartamento',
                'Apertura remota autorizada por el residente',
                'Migración por etapas sin dejar torres incomunicadas',
                'Red de datos para la citofonía en zonas comunes',
            ],
            'paquete' => 'inteligente',
            'faqs' => [
                ['question' => '¿Los residentes tienen que comprar un equipo?', 'answer' => 'No es obligatorio. La llamada puede llegar a una app gratuita en el celular; el monitor interior es opcional para quien lo quiera.'],
                ['question' => '¿Funciona si el residente no está en casa?', 'answer' => 'Sí. La llamada llega a la app por internet y el residente puede hablar con el visitante o autorizar la entrada desde donde esté.'],
                ['question' => '¿Cuánto tiempo toma cambiar la citofonía de una unidad?', 'answer' => 'Depende del número de torres. Trabajamos torre por torre para que ningún apartamento quede sin comunicación con la portería.'],
            ],
        ],
        'mantenimiento' => [
            'name' => 'Mantenimiento de seguridad',
            'short' => 'mantenimiento de seguridad',
            'icon' => 'wrench',
            'image_service' => 'camaras-4k',
            'related_slugs' => [],
            'h1' => 'Mantenimiento de cámaras y control de acceso para unidades residenciales',
            'tagline' => 'Visita preventiva mensual, soporte 24/7 e informe al consejo. Los equipos que ya tiene la unidad vuelven a funcionar como el día que se instalaron.',
            'intro' => 'Una cámara sucia, un disco lleno o un lector dañado se descubren el día del robo. El mantenimiento preventivo revisa todo cada mes y entrega un informe que el administrador puede presentar al consejo, con lo que se hizo y lo que conviene cambiar.',
            'includes' => [
                'Visita preventiva mensual con lista de chequeo',
                'Limpieza y ajuste de cámaras y lectores',
                'Revisión de grabaciones, discos y fuentes de energía',
                'Soporte 24/7 con tiempo de respuesta por contrato',
                'Informe mensual para el consejo de administración',
                'Atención a equipos de cualquier marca e instalador',
            ],
            'paquete' => null,
            'faqs' => [
                ['question' => '¿Atienden equipos que instaló otra empresa?', 'answer' => 'Sí. Hacemos un inventario inicial de lo que tiene la unidad, sin importar la marca o el instalador, y desde ahí arranca el plan de mantenimiento.'],
                ['question' => '¿Qué incluye el informe mensual?', 'answer' => 'Estado de cada equipo, fallas atendidas, tiempos de respuesta y recomendaciones con prioridad, en un PDF corto para el consejo.'],
                ['question' => '¿Hay un tiempo mínimo de contrato?', 'answer' => 'Proponemos contratos anuales para planear las visitas, con salida anticipada si no cumplimos los tiempos de respuesta acordados.'],
            ],
        ],
    ],

    'municipios' => [
        'medellin' => [
            'name' => 'Medellín',
            'sectores' => ['El Poblado', 'Laureles', 'Belén', 'La América', 'Robledo', 'Buenos Aires', 'Castilla'],
            'vecinos' => ['envigado', 'bello', 'itagui'],
            'contexto' => 'En Medellín atendemos unidades en todas las comunas: desde edificios en ladera en El Poblado hasta unidades grandes en Belén, Laureles o Robledo. En todas se repite lo mismo: porterías con alto flujo de domicilios y visitantes, y residentes que esperan resolver todo desde el celular.',
            'match' => ['medellín', 'medellin', 'poblado', 'laureles', 'belén', 'belen', 'robledo'],
        ],
        'envigado' => [
            'name' => 'Envigado',
            'sectores' => ['Zúñiga', 'El Esmeraldal', 'Loma del Escobero', 'La Magnolia', 'Otra Parte'],
            'vecinos' => ['sabaneta', 'medellin', 'itagui'],
            'contexto' => 'Envigado combina unidades de los años noventa y dos mil en la zona plana con torres nuevas en las lomas. Es nuestro municipio sede, así que el equipo técnico está a pocos minutos de cualquier conjunto.',
            'match' => ['envigado'],
        ],
        'sabaneta' => [
            'name' => 'Sabaneta',
            'sectores' => ['Aves María', 'La Doctora', 'Cañaveralejo', 'Las Lomitas', 'Calle Larga'],
            'vecinos' => ['envigado', 'la-estrella', 'itagui'],
            'contexto' => 'Sabaneta ha crecido en vertical, con muchas torres nuevas en poco tiempo. Varias unidades recién entregadas reciben equipos básicos de la constructora y necesitan completarlos antes de la primera asamblea.',
            'match' => ['sabaneta'],
        ],
        'itagui' => [
            'name' => 'Itagüí',
            'sectores' => ['Ditaires', 'Suramérica', 'Santa María', 'San Pío X', 'Autopista Sur'],
            'vecinos' => ['envigado', 'sabaneta', 'la-estrella'],
            'contexto' => 'Itagüí tiene urbanizaciones de muchas torres y parqueaderos amplios, donde un solo punto ciego en el parqueadero o el shut de basuras concentra buena parte de las quejas.',
            'match' => ['itagüí', 'itagui'],
        ],
        'la-estrella' => [
            'name' => 'La Estrella',
            'sectores' => ['Ancón', 'La Tablaza', 'Pueblo Viejo', 'Bellavista', 'Centro'],
            'vecinos' => ['sabaneta', 'itagui', 'envigado'],
            'contexto' => 'La Estrella recibe proyectos nuevos de vivienda con perímetros amplios y zonas verdes, donde el control del cerramiento y de los accesos vehiculares pesa tanto como la portería.',
            'match' => ['la estrella'],
        ],
        'bello' => [
            'name' => 'Bello',
            'sectores' => ['Niquía', 'Cabañas', 'Navarra', 'Hato Viejo', 'Fontidueño'],
            'vecinos' => ['medellin', 'itagui', 'envigado'],
            'contexto' => 'Bello concentra grandes urbanizaciones, sobre todo en Niquía, con cientos de apartamentos por unidad. Ahí la prioridad es saber quién entra y que la portería no colapse en horas pico.',
            'match' => ['bello', 'niquía', 'niquia'],
        ],
    ],

    // Otros municipios que atendemos (aparecen en el formulario; sin página propia por ahora).
    'cobertura_extra' => [
        'caldas' => 'Caldas',
        'copacabana' => 'Copacabana',
        'guarne' => 'Guarne',
        'rionegro' => 'Rionegro',
    ],

    // Enfoque específico servicio × municipio (hace única cada página).
    'enfoque' => [
        'camaras-de-seguridad' => [
            'envigado' => 'En Envigado el reto típico es renovar sistemas analógicos con varios años encima, sin romper fachadas ni zonas comunes ya terminadas.',
            'sabaneta' => 'En las unidades nuevas de Sabaneta solemos completar lo que dejó la constructora: más cámaras en sótanos y parqueaderos, y grabación de varios días con acceso remoto.',
            'medellin' => 'En Medellín priorizamos el reconocimiento de rostros en porterías y de placas en rampas de sótano, donde el flujo de vehículos y domicilios es constante.',
            'itagui' => 'En las urbanizaciones de Itagüí el foco está en parqueaderos extensos y shut de basuras, con cámaras de largo alcance que cubren más área con menos equipos.',
            'la-estrella' => 'En La Estrella cubrimos cerramientos largos y zonas verdes con cámaras perimetrales y visión nocturna, además de porterías y parqueaderos.',
            'bello' => 'En las unidades grandes de Bello diseñamos el sistema por torre y por parqueadero, con monitoreo centralizado para que un solo guarda vea todo.',
        ],
        'control-de-acceso' => [
            'envigado' => 'En Envigado muchas unidades aún usan tarjetas de proximidad fáciles de clonar; migrarlas a biometría o a tag cifrado es la mejora más rápida.',
            'sabaneta' => 'En Sabaneta las unidades nuevas necesitan control de visitantes desde el primer día: QR temporal para domicilios y tags vehiculares para residentes.',
            'medellin' => 'En Medellín el residente espera autorizar visitas desde el celular, así que combinamos el control de acceso con QR temporal enviado por el propio residente.',
            'itagui' => 'En Itagüí, con varios accesos y muchos vehículos, la lectura de tag UHF en la portería vehicular elimina las filas en horas pico.',
            'la-estrella' => 'En La Estrella el control de accesos vehiculares y peatonales en perímetros amplios evita ingresos por puntos del cerramiento sin vigilancia.',
            'bello' => 'En Bello, con cientos de apartamentos, registrar cada ingreso y dar de baja al instante los tags perdidos devuelve el control a la administración.',
        ],
        'citofonia-ip' => [
            'envigado' => 'En Envigado reemplazamos citofonía análoga sin repuestos, torre por torre, para que ningún apartamento quede incomunicado durante la obra.',
            'sabaneta' => 'En las unidades nuevas de Sabaneta, la citofonía IP con app permite atender visitas aunque el residente esté trabajando, algo clave en torres con muchos domicilios.',
            'medellin' => 'En Medellín, con alto flujo de domicilios, la llamada con video a la app del residente acorta mucho el tiempo de atención en portería.',
            'itagui' => 'En las urbanizaciones de Itagüí la citofonía IP unifica varias porterías en una sola consola, sin cablear de nuevo cada apartamento.',
            'la-estrella' => 'En La Estrella la citofonía IP conecta con los residentes las porterías alejadas de las torres, por red de datos y sin depender de cableado antiguo.',
            'bello' => 'En Bello la citofonía IP permite atender cientos de apartamentos desde una consola, con la llamada directo al celular del residente.',
        ],
        'mantenimiento' => [
            'envigado' => 'Como estamos en Envigado, atendemos fallas en sitio con tiempos cortos y hacemos la visita preventiva en el horario que acuerde la administración.',
            'sabaneta' => 'En Sabaneta empezamos con un inventario de lo que entregó la constructora, para saber qué tiene garantía y qué conviene cubrir con mantenimiento.',
            'medellin' => 'En Medellín programamos las visitas en horarios de bajo flujo para no interrumpir la operación de la portería.',
            'itagui' => 'En Itagüí priorizamos discos de grabación y cámaras de parqueadero, que son los equipos que más fallan por polvo y humedad.',
            'la-estrella' => 'En La Estrella revisamos cámaras perimetrales y fuentes de energía, expuestas a lluvia y a cortes en zonas abiertas.',
            'bello' => 'En las unidades grandes de Bello, el informe mensual al consejo muestra torre por torre el estado de los equipos y lo que se atendió.',
        ],
    ],
];
