<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('company')->nullable()->after('email');
            $table->string('employees_range', 50)->nullable()->after('company');
            $table->string('current_it', 100)->nullable()->after('employees_range');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['company', 'employees_range', 'current_it']);
        });
    }
};
