<?php

/**
 * Facturación electrónica DIAN — módulo portado (inactivo hasta configurar).
 *
 * No envía nada a la DIAN hasta que:
 *  - settings.dian_* estén completos
 *  - exista resolución activa en dian_resolutions
 *  - haya certificado .p12 en storage
 *
 * Flujo previsto: Quote → ElectronicInvoice → DianService::sendInvoice()
 */
return [
    'enabled' => (bool) env('DIAN_ENABLED', false),

    /** Ambiente por defecto si settings aún no existen: 2 = Habilitación */
    'default_environment' => (int) env('DIAN_ENVIRONMENT', 2),
];
