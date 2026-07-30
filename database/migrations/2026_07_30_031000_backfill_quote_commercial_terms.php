<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('quotes')
            ->whereNull('payment_terms')
            ->update(['payment_terms' => config('quotes.default_payment_terms')]);

        DB::table('quotes')
            ->whereNull('warranty_terms')
            ->update(['warranty_terms' => config('quotes.default_warranty_terms')]);
    }

    public function down(): void
    {
        // Existing commercial terms are intentionally preserved.
    }
};
