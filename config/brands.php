<?php

/**
 * Mapa slug-de-marca => dominio, para resolver logos vía
 * https://www.google.com/s2/favicons?domain={dominio}&sz=128
 * Marcas no mapeadas se muestran con monograma (fallback).
 * Para usar un logo propio, colocar public/img/brands/{slug}.png (tiene prioridad).
 */
return [
    'domains' => [
        // CCTV
        'hikvision'        => 'hikvision.com',
        'hikvision-ax-pro' => 'hikvision.com',
        'hilook'           => 'hikvision.com',
        'dahua'            => 'dahuasecurity.com',
        'axis'             => 'axis.com',
        'hanwha'           => 'hanwhavision.com',
        'vivotek'          => 'vivotek.com',
        'ezviz'            => 'ezviz.com',
        'imou'             => 'imoulife.com',
        // Solar
        'canadian-solar'   => 'canadiansolar.com',
        'ja-solar'         => 'jasolar.com',
        'trina'            => 'trinasolar.com',
        'longi'            => 'longi.com',
        'huawei'           => 'huawei.com',
        'huawei-luna'      => 'huawei.com',
        'growatt'          => 'growatt.com',
        'fronius'          => 'fronius.com',
        'sungrow'          => 'sungrowpower.com',
        'pylontech'        => 'pylontech.com.cn',
        'byd'              => 'byd.com',
        // Control de acceso / automatización de puertas
        'zkteco'           => 'zkteco.com',
        'suprema'          => 'supremainc.com',
        'anviz'            => 'anviz.com',
        'armatura'         => 'armatura.us',
        'bft'              => 'bft-automation.com',
        'came'             => 'came.com',
        'nice'             => 'niceforyou.com',
        'faac'             => 'faac.com',
        // Alarmas
        'ajax'             => 'ajax.systems',
        'dsc'              => 'dsc.com',
        'paradox'          => 'paradox.com',
        // Domótica
        'shelly'           => 'shelly.com',
        'sonoff'           => 'sonoff.tech',
        'knx'              => 'knx.org',
        'control4'         => 'control4.com',
        'philips-hue'      => 'philips-hue.com',
        'tuya'             => 'tuya.com',
        'amazon-alexa'     => 'amazon.com',
        'google-home'      => 'home.google.com',
        // Redes
        'ubiquiti'                 => 'ui.com',
        'ubiquiti-unifi'           => 'ui.com',
        'ubiquiti-airfiber-airmax' => 'ui.com',
        'mikrotik'         => 'mikrotik.com',
        'cisco'            => 'cisco.com',
        'tp-link-omada'    => 'tp-link.com',
        'panduit'          => 'panduit.com',
        'furukawa'         => 'furukawalatam.com',
        'siemon'           => 'siemon.com',
        'corning'          => 'corning.com',
        'commscope'        => 'commscope.com',
        'cambium'          => 'cambiumnetworks.com',
        'mimosa'           => 'mimosa.co',
        'siklu'            => 'siklu.com',
        // Citofonía / IPTV
        'akuvox'           => 'akuvox.com',
        'fanvil'           => 'fanvil.com',
        'grandstream'      => 'grandstream.com',
        'fmuser'           => 'fmuser.org',
        // Detección de incendios
        'notifier'         => 'notifier.com',
        'honeywell'        => 'honeywell.com',
        'edwards'          => 'edwardsfiresafety.com',
        'bosch'            => 'boschsecurity.com',
        'hochiki'          => 'hochikiamerica.com',
        'gst'              => 'gst.com',
        'souka'            => 'soukatec.com',
        'accespro'         => 'accespro.com',
        'horus'            => 'horus.es',
        'amp'              => 'amphenol.com',
        'lanpro'           => 'lanpro.com',
        'linkedpro'        => 'linkedpro.com',
        'autosolar'        => 'autosolar.co',
        'tb-plus'          => 'tbplus.info',
    ],

    /** URL directa cuando favicon.ico del dominio no sirve */
    'overrides' => [
        'hilook'           => 'https://www.google.com/s2/favicons?domain=hikvision.com&sz=128',
        'zkteco'           => 'https://www.google.com/s2/favicons?domain=zkteco.com&sz=128',
        'ajax'             => 'https://www.google.com/s2/favicons?domain=ajax.systems&sz=128',
        'axis'             => 'https://www.google.com/s2/favicons?domain=axis.com&sz=128',
        'hikvision-ax-pro' => 'https://www.google.com/s2/favicons?domain=hikvision.com&sz=128',
        'ja-solar'         => 'https://www.jasolar.com/favicon.ico',
        'growatt'          => 'https://www.growatt.com/favicon.ico',
        'canadian-solar'   => 'https://www.canadiansolar.com/favicon.ico',
        'longi'            => 'https://www.longi.com/favicon.ico',
        'trina'            => 'https://www.trinasolar.com/favicon.ico',
        'pylontech'        => 'https://www.pylontech.com.cn/favicon.ico',
        'shelly'           => 'https://www.shelly.com/favicon.ico',
        'suprema'          => 'https://www.supremainc.com/favicon.ico',
        'mikrotik'         => 'https://mikrotik.com/favicon.ico',
        'notifier'         => 'https://www.notifier.com/favicon.ico',
        'came'             => 'https://www.google.com/s2/favicons?domain=came.com&sz=128',
        'hochiki'          => 'https://www.hochikiamerica.com/favicon.ico',
    ],
];
