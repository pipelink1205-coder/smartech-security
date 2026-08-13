<?php

namespace App\Services\Dian;

use App\Models\DianResolution;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * Lectura y persistencia de la configuración DIAN (tabla `settings`).
 *
 * Se edita desde Administración → Configuración DIAN.
 */
class DianConfig
{
    public const WSDL_HABILITACION = 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc?wsdl';
    public const WSDL_PRODUCCION = 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc?wsdl';

    public const ENDPOINT_HABILITACION = 'https://vpfe-hab.dian.gov.co/WcfDianCustomerServices.svc';
    public const ENDPOINT_PRODUCCION = 'https://vpfe.dian.gov.co/WcfDianCustomerServices.svc';

    public const QR_URL_HABILITACION = 'https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=';
    public const QR_URL_PRODUCCION = 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentkey=';

    public const EDITABLE_KEYS = [
        'dian_enabled',
        'dian_environment',
        'dian_company_nit',
        'dian_company_dv',
        'dian_company_razon_social',
        'dian_company_nombre_comercial',
        'dian_company_tipo_documento',
        'dian_company_tipo_persona',
        'dian_company_regimen',
        'dian_company_responsabilidad',
        'dian_company_address',
        'dian_company_city_code',
        'dian_company_dept_code',
        'dian_company_country_code',
        'dian_company_phone',
        'dian_company_email',
        'dian_company_actividad_economica',
        'dian_company_municipio_nombre',
        'dian_software_id',
        'dian_software_pin',
        'dian_test_set_id',
        'dian_clave_tecnica',
        'dian_cert_path',
        'dian_cert_password',
        'iva_rate',
        'ico_rate',
    ];

    /** Claves que no se reescriben si el formulario las deja en blanco. */
    private const SECRET_KEYS = [
        'dian_software_pin',
        'dian_cert_password',
    ];

    /** @var array<string, string|null> */
    private array $cache = [];

    public function __construct()
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $this->cache = Setting::query()->pluck('value', 'key')->all();
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $value = $this->cache[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return (string) $value;
    }

    public function environment(): int
    {
        return (int) ($this->get('dian_environment') ?? config('dian.default_environment', 2));
    }

    public function isProduction(): bool
    {
        return $this->environment() === 1;
    }

    public function endpoint(): string
    {
        return $this->isProduction() ? self::ENDPOINT_PRODUCCION : self::ENDPOINT_HABILITACION;
    }

    public function wsdl(): string
    {
        return $this->isProduction() ? self::WSDL_PRODUCCION : self::WSDL_HABILITACION;
    }

    public function qrBaseUrl(): string
    {
        return $this->isProduction() ? self::QR_URL_PRODUCCION : self::QR_URL_HABILITACION;
    }

    public function ivaFactor(): float
    {
        return ((float) ($this->get('iva_rate') ?? 19)) / 100;
    }

    public function icoFactor(): float
    {
        return ((float) ($this->get('ico_rate') ?? 0)) / 100;
    }

    /** @return array<string, string|null> */
    public function emisor(): array
    {
        return [
            'nit' => $this->get('dian_company_nit'),
            'dv' => $this->get('dian_company_dv'),
            'razon_social' => $this->get('dian_company_razon_social'),
            'nombre_comercial' => $this->get('dian_company_nombre_comercial'),
            'tipo_documento' => $this->get('dian_company_tipo_documento', '31'),
            'tipo_persona' => $this->get('dian_company_tipo_persona', '1'),
            'regimen' => $this->get('dian_company_regimen', '49'),
            'responsabilidad_fiscal' => $this->get('dian_company_responsabilidad', 'R-99-PN'),
            'address' => $this->get('dian_company_address'),
            'city_code' => $this->get('dian_company_city_code', '11001'),
            'dept_code' => $this->get('dian_company_dept_code', '11'),
            'country_code' => $this->get('dian_company_country_code', 'CO'),
            'phone' => $this->get('dian_company_phone'),
            'email' => $this->get('dian_company_email'),
            'actividad_economica' => $this->get('dian_company_actividad_economica'),
            'municipio_nombre' => $this->get('dian_company_municipio_nombre'),
        ];
    }

    /** @return array{id: ?string, pin: ?string} */
    public function software(): array
    {
        return [
            'id' => $this->get('dian_software_id'),
            'pin' => $this->get('dian_software_pin'),
        ];
    }

    public function testSetId(): ?string
    {
        return $this->get('dian_test_set_id');
    }

    public function certPath(): ?string
    {
        return $this->get('dian_cert_path');
    }

    public function hasCertificateFile(): bool
    {
        $path = $this->certPath();

        return filled($path) && Storage::disk('local')->exists($path);
    }

    public function certPassword(): ?string
    {
        $raw = $this->get('dian_cert_password');

        if (blank($raw)) {
            return null;
        }

        try {
            return Crypt::decryptString($raw);
        } catch (\Throwable) {
            return $raw;
        }
    }

    public function hasCertPassword(): bool
    {
        return filled($this->get('dian_cert_password'));
    }

    public function hasSoftwarePin(): bool
    {
        return filled($this->get('dian_software_pin'));
    }

    /**
     * Clave técnica para CUFE: resolución activa, luego setting, luego PIN del software.
     */
    public function claveTecnica(): string
    {
        $fromResolution = DianResolution::active()?->clave_tecnica;
        if (filled($fromResolution)) {
            return (string) $fromResolution;
        }

        if (filled($this->get('dian_clave_tecnica'))) {
            return (string) $this->get('dian_clave_tecnica');
        }

        return (string) ($this->software()['pin'] ?? '');
    }

    /**
     * Kill switch de .env (DIAN_ENABLED) AND interruptor del admin.
     */
    public function isEnabled(): bool
    {
        if (! config('dian.enabled')) {
            return false;
        }

        return filter_var($this->get('dian_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function envKillSwitchOn(): bool
    {
        return (bool) config('dian.enabled');
    }

    public function adminToggleOn(): bool
    {
        return filter_var($this->get('dian_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
    }

    public function isConfigured(): bool
    {
        return $this->missingRequirements() === [];
    }

    public function canEmit(): bool
    {
        return $this->isEnabled() && $this->isConfigured();
    }

    /**
     * @return list<string>
     */
    public function missingRequirements(): array
    {
        $missing = [];

        if (blank($this->get('dian_company_nit')) || blank($this->get('dian_company_razon_social'))) {
            $missing[] = 'Datos de la empresa (NIT y razón social)';
        }

        if (blank($this->get('dian_software_id')) || ! $this->hasSoftwarePin()) {
            $missing[] = 'Software DIAN (ID y PIN)';
        }

        if (! $this->hasCertificateFile()) {
            $missing[] = 'Certificado digital .p12 / .pfx';
        }

        if (! $this->hasCertPassword()) {
            $missing[] = 'Contraseña del certificado';
        }

        if (! $this->isProduction() && blank($this->testSetId())) {
            $missing[] = 'TestSetId de habilitación';
        }

        if (! DianResolution::active()) {
            $missing[] = 'Resolución de numeración activa para este ambiente';
        }

        return $missing;
    }

    /**
     * @return list<array{ok: bool, label: string, hint: ?string}>
     */
    public function checklist(): array
    {
        return [
            [
                'ok' => $this->envKillSwitchOn(),
                'label' => 'Kill switch .env (DIAN_ENABLED=true)',
                'hint' => 'En el servidor, en .env, pon DIAN_ENABLED=true. Sin eso el admin no puede enviar.',
            ],
            [
                'ok' => $this->adminToggleOn(),
                'label' => 'Interruptor de envío en este panel',
                'hint' => 'Activa “Habilitar envío a DIAN” abajo cuando el resto esté listo.',
            ],
            [
                'ok' => filled($this->get('dian_company_nit')) && filled($this->get('dian_company_razon_social')),
                'label' => 'Empresa (NIT y razón social)',
                'hint' => null,
            ],
            [
                'ok' => filled($this->get('dian_software_id')) && $this->hasSoftwarePin(),
                'label' => 'Software DIAN (ID y PIN)',
                'hint' => 'Salen del registro del software en MUISCA / factura electrónica.',
            ],
            [
                'ok' => $this->hasCertificateFile(),
                'label' => 'Certificado .p12 cargado',
                'hint' => 'Archivo ONAC. Se guarda fuera de la carpeta pública.',
            ],
            [
                'ok' => $this->hasCertPassword(),
                'label' => 'Contraseña del certificado',
                'hint' => null,
            ],
            [
                'ok' => $this->isProduction() || filled($this->testSetId()),
                'label' => $this->isProduction() ? 'TestSetId (no aplica en producción)' : 'TestSetId de habilitación',
                'hint' => 'Lo entrega la DIAN al habilitar el software.',
            ],
            [
                'ok' => DianResolution::active() !== null,
                'label' => 'Resolución de numeración activa',
                'hint' => 'Administración → Resoluciones DIAN.',
            ],
        ];
    }

    public function statusHtml(): string
    {
        $items = $this->checklist();
        $html = '<ul class="grid gap-1.5 text-sm">';

        foreach ($items as $item) {
            $color = $item['ok'] ? 'text-success-600 dark:text-success-400' : 'text-gray-500 dark:text-gray-400';
            $mark = $item['ok'] ? '✓' : '○';
            $hint = (! $item['ok'] && filled($item['hint'])) ? ' — '.e($item['hint']) : '';
            $html .= '<li class="'.$color.'"><span class="font-semibold">'.$mark.'</span> '.e($item['label']).$hint.'</li>';
        }

        return $html.'</ul>';
    }

    /**
     * Estado inicial del formulario (secretos vacíos para no revelarlos).
     *
     * @return array<string, mixed>
     */
    public function formState(): array
    {
        $state = [];

        foreach (self::EDITABLE_KEYS as $key) {
            if (in_array($key, self::SECRET_KEYS, true)) {
                $state[$key] = '';

                continue;
            }

            if ($key === 'dian_enabled') {
                $state[$key] = $this->adminToggleOn();

                continue;
            }

            if ($key === 'dian_cert_path') {
                $state[$key] = $this->hasCertificateFile() ? $this->certPath() : null;

                continue;
            }

            $state[$key] = $this->get($key, match ($key) {
                'dian_environment' => (string) config('dian.default_environment', 2),
                'dian_company_tipo_documento' => '31',
                'dian_company_tipo_persona' => '1',
                'dian_company_regimen' => '48',
                'dian_company_responsabilidad' => 'O-13',
                'dian_company_city_code' => '05001',
                'dian_company_dept_code' => '05',
                'dian_company_country_code' => 'CO',
                'dian_company_municipio_nombre' => 'MEDELLIN',
                'iva_rate' => '19',
                'ico_rate' => '0',
                default => '',
            });
        }

        return $state;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function save(array $data): void
    {
        foreach (self::EDITABLE_KEYS as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }

            $value = $this->normalizeValue($key, $data[$key]);

            if (in_array($key, self::SECRET_KEYS, true) && $value === '') {
                continue;
            }

            if ($key === 'dian_cert_password' && $value !== '') {
                $value = Crypt::encryptString($value);
            }

            Setting::setValue($key, $value);
        }

        $this->refresh();
    }

    private function normalizeValue(string $key, mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        if ($key === 'dian_enabled') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
        }

        if ($key === 'dian_environment') {
            return ((int) $value) === 1 ? '1' : '2';
        }

        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return trim((string) $value);
    }
}
