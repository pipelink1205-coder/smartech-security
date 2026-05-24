<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('service');
            $table->string('zone', 80)->nullable();
            $table->text('message')->nullable();
            $table->decimal('price_min', 12, 2)->nullable();
            $table->decimal('price_max', 12, 2)->nullable();
            $table->enum('status', ['new','contacted','quoted','closed','lost'])->default('new');
            $table->text('notes')->nullable();          // Notas internas del admin
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('quotes'); }
};
