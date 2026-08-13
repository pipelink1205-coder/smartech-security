<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            'dian_company_nit'                 => '',
            'dian_company_dv'                  => '',
            'dian_company_razon_social'        => '',
            'dian_company_nombre_comercial'    => '',
            'dian_company_tipo_documento'      => '31',
            'dian_company_tipo_persona'        => '1',
            'dian_company_regimen'             => '48',
            'dian_company_responsabilidad'     => 'O-13',
            'dian_company_address'             => '',
            'dian_company_city_code'           => '05001',
            'dian_company_dept_code'           => '05',
            'dian_company_country_code'        => 'CO',
            'dian_company_phone'               => '',
            'dian_company_email'               => '',
            'dian_company_actividad_economica' => '',
            'dian_company_municipio_nombre'    => 'MEDELLIN',

            'dian_environment' => '2',
            'dian_test_set_id' => '',
            'dian_software_id' => '',
            'dian_software_pin'=> '',
            'dian_clave_tecnica' => '',

            'dian_cert_path'     => '',
            'dian_cert_password' => '',

            'iva_rate' => '19',
            'ico_rate' => '0',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->where('key', 'like', 'dian_%')->delete();
        DB::table('settings')->whereIn('key', ['iva_rate', 'ico_rate'])->delete();
    }
};
