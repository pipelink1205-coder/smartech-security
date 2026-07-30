<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_catalog_items', function (Blueprint $table) {
            $table->string('type', 24)->default('product')->after('id');
            $table->string('name')->nullable()->after('code');
            $table->string('unit', 24)->default('unidad')->after('description');
            $table->decimal('default_tax_rate', 5, 2)->default(19)->after('default_unit_price');
            $table->string('category', 80)->nullable()->after('default_tax_rate');
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->foreignId('quote_catalog_item_id')
                ->nullable()
                ->after('quote_id')
                ->constrained('quote_catalog_items')
                ->nullOnDelete();
            $table->string('type', 24)->default('product')->after('code');
            $table->string('concept')->nullable()->after('type');
            $table->string('unit', 24)->default('unidad')->after('quantity');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->decimal('tax_rate', 5, 2)->default(19)->after('discount_percent');
            $table->decimal('gross_subtotal', 14, 2)->default(0)->after('tax_rate');
            $table->decimal('discount_amount', 14, 2)->default(0)->after('gross_subtotal');
            $table->decimal('net_subtotal', 14, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 14, 2)->default(0)->after('net_subtotal');
            $table->decimal('line_total', 14, 2)->default(0)->after('tax_amount');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('currency', 3)->default('COP')->after('tax_percent');
            $table->decimal('subtotal', 14, 2)->default(0)->after('currency');
            $table->decimal('discount_total', 14, 2)->default(0)->after('subtotal');
            $table->decimal('tax_total', 14, 2)->default(0)->after('discount_total');
            $table->decimal('grand_total', 14, 2)->default(0)->after('tax_total');
            $table->text('payment_terms')->nullable()->after('terms');
            $table->text('warranty_terms')->nullable()->after('payment_terms');
            $table->string('advisor_name', 120)->nullable()->after('warranty_terms');
            $table->string('advisor_title', 120)->nullable()->after('advisor_name');
            $table->timestamp('issued_at')->nullable()->after('advisor_title');
            $table->timestamp('sent_at')->nullable()->after('issued_at');
            $table->timestamp('accepted_at')->nullable()->after('sent_at');
        });

        // Preserve the existing catalog and formal quote data.
        \DB::table('quote_catalog_items')->whereNull('name')->update([
            'name' => \DB::raw('description'),
        ]);

        \DB::table('quote_items')->whereNull('concept')->update([
            'concept' => \DB::raw('description'),
        ]);

        \DB::table('quote_items')->orderBy('id')->eachById(function ($item): void {
            $gross = round((float) $item->quantity * (float) $item->unit_price, 2);
            $discount = round($gross * ((float) $item->discount_percent / 100), 2);
            $net = round($gross - $discount, 2);
            $tax = round($net * ((float) $item->tax_rate / 100), 2);

            \DB::table('quote_items')->where('id', $item->id)->update([
                'gross_subtotal' => $gross,
                'discount_amount' => $discount,
                'net_subtotal' => $net,
                'tax_amount' => $tax,
                'line_total' => round($net + $tax, 2),
            ]);
        });

        \DB::table('quotes')->orderBy('id')->eachById(function ($quote): void {
            $totals = \DB::table('quote_items')
                ->where('quote_id', $quote->id)
                ->selectRaw('COALESCE(SUM(gross_subtotal), 0) subtotal')
                ->selectRaw('COALESCE(SUM(discount_amount), 0) discount_total')
                ->selectRaw('COALESCE(SUM(tax_amount), 0) tax_total')
                ->selectRaw('COALESCE(SUM(line_total), 0) grand_total')
                ->first();

            \DB::table('quotes')->where('id', $quote->id)->update([
                'subtotal' => $totals->subtotal,
                'discount_total' => $totals->discount_total,
                'tax_total' => $totals->tax_total,
                'grand_total' => $totals->grand_total,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'currency',
                'subtotal',
                'discount_total',
                'tax_total',
                'grand_total',
                'payment_terms',
                'warranty_terms',
                'advisor_name',
                'advisor_title',
                'issued_at',
                'sent_at',
                'accepted_at',
            ]);
        });

        Schema::table('quote_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('quote_catalog_item_id');
            $table->dropColumn([
                'type',
                'concept',
                'unit',
                'discount_percent',
                'tax_rate',
                'gross_subtotal',
                'discount_amount',
                'net_subtotal',
                'tax_amount',
                'line_total',
            ]);
        });

        Schema::table('quote_catalog_items', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'name',
                'unit',
                'default_tax_rate',
                'category',
            ]);
        });
    }
};
