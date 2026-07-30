<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('intent', 20)->default('info')->after('message');
            $table->date('preferred_visit_date')->nullable()->after('intent');
            $table->string('preferred_visit_slot', 20)->nullable()->after('preferred_visit_date');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['intent', 'preferred_visit_date', 'preferred_visit_slot']);
        });
    }
};
