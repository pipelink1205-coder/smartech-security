<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_covers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('kind', 32)->default('institucional');
            $table->string('badge')->nullable();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('cta_primary_label')->default('Ver Servicios');
            $table->string('cta_primary_url')->default('#servicios');
            $table->string('cta_secondary_label')->default('Agendar visita gratuita');
            $table->string('cta_secondary_url')->default('/?intent=visit#contacto');
            $table->string('strip_label')->nullable();
            $table->json('strip_highlights')->nullable();
            $table->json('stats')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_fallback')->default(false);
            $table->unsignedTinyInteger('priority')->default(10);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'is_fallback', 'priority']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_covers');
    }
};
