<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedSmallInteger('portrait_scale')->default(84)->change();
        });

        DB::table('employees')->where('portrait_scale', 100)->update(['portrait_scale' => 84]);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedSmallInteger('portrait_scale')->default(100)->change();
        });

        DB::table('employees')->where('portrait_scale', 84)->update(['portrait_scale' => 100]);
    }
};
