<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Facturas electrónicas DIAN (sin tabla orders del POS).
 *
 * Estados: PENDING | SIGNED | SENT | ACCEPTED | REJECTED | ERROR
 * document_type 01 = Factura electrónica de venta
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('electronic_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->string('document_type', 5)->default('01');

            $table->string('dian_prefijo', 10)->nullable();
            $table->unsignedBigInteger('dian_numero')->nullable();
            $table->foreignId('dian_resolution_id')->nullable()
                ->constrained('dian_resolutions')->nullOnDelete();

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('iva', 14, 2)->default(0);
            $table->decimal('ico', 14, 2)->default(0);
            $table->decimal('descuento_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('total_a_pagar', 14, 2)->default(0);
            $table->string('payment_method', 30)->nullable();

            $table->string('client_name')->nullable();
            $table->string('client_document', 40)->nullable();
            $table->string('client_tipo_documento', 5)->nullable();
            $table->string('client_dv', 2)->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone', 40)->nullable();
            $table->string('client_address')->nullable();
            $table->string('client_city_code', 10)->nullable();
            $table->string('client_dept_code', 10)->nullable();

            $table->string('dian_status', 30)->default('PENDING');
            $table->string('cufe', 96)->nullable();
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

            $table->unique(['dian_prefijo', 'dian_numero'], 'ei_dian_prefijo_numero_unique');
        });

        Schema::create('electronic_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('electronic_invoice_id')
                ->constrained('electronic_invoices')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 14, 2)->default(1);
            $table->decimal('price', 14, 2)->default(0); // precio unitario con IVA (como en el origen)
            $table->foreignId('quote_item_id')->nullable()->constrained('quote_items')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electronic_invoice_items');
        Schema::dropIfExists('electronic_invoices');
    }
};
