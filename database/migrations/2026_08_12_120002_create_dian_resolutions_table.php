<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dian_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('environment')->default(2); // 1=Producción, 2=Habilitación
            $table->string('numero_resolucion', 30);
            $table->date('fecha_resolucion')->nullable();
            $table->string('prefijo', 10)->default('SETP');
            $table->unsignedBigInteger('rango_desde');
            $table->unsignedBigInteger('rango_hasta');
            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->string('clave_tecnica', 150)->nullable();
            $table->unsignedBigInteger('consecutivo_actual')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dian_resolutions');
    }
};
