<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dian_credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electronic_invoice_id')
                ->constrained('electronic_invoices')->cascadeOnDelete();

            $table->string('prefijo', 10);
            $table->unsignedBigInteger('numero');
            $table->foreignId('dian_resolution_id')->nullable()
                ->constrained('dian_resolutions')->nullOnDelete();

            $table->unsignedTinyInteger('reason_code');
            $table->string('reason_description');

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('iva', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            $table->string('dian_status', 30)->default('PENDING');
            $table->string('cude', 96)->nullable();
            $table->string('dian_zip_id', 50)->nullable();
            $table->string('dian_response_code', 10)->nullable();
            $table->text('dian_description')->nullable();
            $table->text('dian_errors')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('ar_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('qr_url', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['prefijo', 'numero'], 'dian_credit_notes_prefijo_numero_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dian_credit_notes');
    }
};
