<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        $services = DB::table('services')->get();

        foreach ($services as $svc) {
            DB::table('projects')
                ->whereNull('service_id')
                ->where(function ($q) use ($svc) {
                    $q->where('category', $svc->name)
                        ->orWhere('category', $svc->slug);
                })
                ->update(['service_id' => $svc->id]);
        }

        $legacyCategories = [
            'Seguridad Corporativa' => 'camaras-4k',
            'Energía Solar' => 'energia-solar',
            'Control de Acceso' => 'control-acceso',
            'Redes Empresariales' => 'redes-fibra',
            'Domótica' => 'domotica',
            'Alarmas' => 'alarmas',
            'Cámaras y Videovigilancia' => 'camaras-4k',
        ];

        foreach ($legacyCategories as $category => $slug) {
            $serviceId = DB::table('services')->where('slug', $slug)->value('id');

            if ($serviceId === null) {
                continue;
            }

            DB::table('projects')
                ->whereNull('service_id')
                ->where('category', $category)
                ->update(['service_id' => $serviceId]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('service_id');
        });
    }
};
