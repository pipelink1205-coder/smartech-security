<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['terms', 'payment_terms', 'warranty_terms'] as $column) {
            DB::table('quotes')
                ->where($column, '0')
                ->update([$column => null]);
        }
    }

    public function down(): void
    {
        // Empty values must remain empty.
    }
};
