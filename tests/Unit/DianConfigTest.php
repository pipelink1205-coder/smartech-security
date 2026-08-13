<?php

namespace Tests\Unit;

use App\Models\DianResolution;
use App\Models\Setting;
use App\Services\Dian\DianConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DianConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_not_configured_until_required_fields_exist(): void
    {
        $config = new DianConfig;

        $this->assertFalse($config->isConfigured());
        $this->assertFalse($config->isEnabled());
        $this->assertNotEmpty($config->missingRequirements());
    }

    public function test_save_persists_company_and_keeps_blank_secrets(): void
    {
        Setting::setValue('dian_software_pin', 'existing-pin');

        $config = new DianConfig;
        $config->save([
            'dian_company_nit' => '901234567',
            'dian_company_dv' => '1',
            'dian_company_razon_social' => 'Smart Tech Security S.A.S.',
            'dian_enabled' => false,
            'dian_environment' => '2',
        ]);

        $fresh = new DianConfig;

        $this->assertSame('901234567', $fresh->get('dian_company_nit'));
        $this->assertSame('Smart Tech Security S.A.S.', $fresh->emisor()['razon_social']);
        $this->assertSame('existing-pin', $fresh->software()['pin']);
        $this->assertFalse($fresh->adminToggleOn());
    }

    public function test_encrypts_certificate_password(): void
    {
        $config = new DianConfig;
        $config->save([
            'dian_cert_password' => 'p12-secret',
        ]);

        $stored = Setting::getValue('dian_cert_password');
        $this->assertNotSame('p12-secret', $stored);
        $this->assertSame('p12-secret', Crypt::decryptString((string) $stored));
        $this->assertSame('p12-secret', (new DianConfig)->certPassword());
        $this->assertSame('', (new DianConfig)->formState()['dian_cert_password']);
    }

    public function test_is_enabled_requires_env_and_admin_toggle(): void
    {
        config(['dian.enabled' => true]);
        Setting::setValue('dian_enabled', '1');

        $this->assertTrue((new DianConfig)->isEnabled());

        config(['dian.enabled' => false]);
        $this->assertFalse((new DianConfig)->isEnabled());
    }

    public function test_is_configured_when_company_software_cert_testset_and_resolution_exist(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('dian/certs/test.p12', 'dummy-cert');

        Setting::setValue('dian_company_nit', '901234567');
        Setting::setValue('dian_company_razon_social', 'Smart Tech Security S.A.S.');
        Setting::setValue('dian_software_id', 'software-uuid');
        Setting::setValue('dian_software_pin', '12345');
        Setting::setValue('dian_test_set_id', 'test-set-id');
        Setting::setValue('dian_cert_path', 'dian/certs/test.p12');
        Setting::setValue('dian_cert_password', 'secret');
        Setting::setValue('dian_environment', '2');

        DianResolution::create([
            'environment' => 2,
            'numero_resolucion' => '18760000001',
            'prefijo' => 'SETP',
            'rango_desde' => 1,
            'rango_hasta' => 100,
            'clave_tecnica' => 'clave-res',
            'consecutivo_actual' => 0,
            'is_active' => true,
        ]);

        $config = new DianConfig;

        $this->assertTrue($config->isConfigured());
        $this->assertSame('clave-res', $config->claveTecnica());
        $this->assertSame([], $config->missingRequirements());
    }

    public function test_clave_tecnica_falls_back_to_setting_then_pin(): void
    {
        Setting::setValue('dian_clave_tecnica', 'from-settings');
        Setting::setValue('dian_software_pin', 'from-pin');

        $this->assertSame('from-settings', (new DianConfig)->claveTecnica());

        Setting::setValue('dian_clave_tecnica', '');
        $this->assertSame('from-pin', (new DianConfig)->claveTecnica());
    }
}
