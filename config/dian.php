<?php

/**
 * Facturación electrónica DIAN.
 *
 * No envía nada hasta que:
 *  - DIAN_ENABLED=true en .env (kill switch)
 *  - el interruptor del admin (settings.dian_enabled) esté encendido
 *  - settings dian_* estén completos (Administración → Configuración DIAN)
 *  - exista resolución activa (Administración → Resoluciones DIAN)
 *  - haya certificado .p12 en storage
 *
 * Flujo: Quote → ElectronicInvoice → DianService::sendInvoice()
 */
return [
    /** Kill switch. Aunque el admin active el interruptor, sin esto no se envía. */
    'enabled' => (bool) env('DIAN_ENABLED', false),

    /** Ambiente por defecto si settings aún no existen: 2 = Habilitación */
    'default_environment' => (int) env('DIAN_ENVIRONMENT', 2),
];
