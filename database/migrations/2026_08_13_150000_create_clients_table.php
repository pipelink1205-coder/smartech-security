<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('company', 180)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('zone', 80)->nullable();
            $table->string('document_type', 5)->nullable();
            $table->string('document', 40)->nullable();
            $table->string('dv', 2)->nullable();
            $table->string('city_code', 10)->nullable();
            $table->string('dept_code', 10)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('company');
            $table->index('email');
            $table->index('phone');
            $table->index('document');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->foreignId('client_id')
                ->nullable()
                ->after('email')
                ->constrained('clients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::dropIfExists('clients');
    }
};
