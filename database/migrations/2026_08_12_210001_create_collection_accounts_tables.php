<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuenta de cobro comercial (no fiscal / no DIAN).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('number', 40)->unique();
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->string('status', 20)->default('draft');

            $table->string('client_name');
            $table->string('client_document', 40)->nullable();
            $table->string('client_email')->nullable();
            $table->string('client_phone', 40)->nullable();
            $table->string('client_address')->nullable();
            $table->string('concept')->nullable();

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_total', 14, 2)->default(0);
            $table->decimal('tax_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);

            $table->string('bank_name')->nullable();
            $table->string('bank_account_type', 30)->nullable();
            $table->string('bank_account_number', 40)->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('bank_nit', 30)->nullable();
            $table->text('payment_instructions')->nullable();
            $table->text('notes')->nullable();

            $table->string('pdf_path')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('collection_account_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_account_id')
                ->constrained('collection_accounts')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 14, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->foreignId('quote_item_id')->nullable()->constrained('quote_items')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_account_items');
        Schema::dropIfExists('collection_accounts');
    }
};
