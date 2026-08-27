<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('verification_token', 64)->nullable()->unique()->after('employee_code');
            $table->string('photo_card')->nullable()->after('photo_original');
            $table->string('authorized_signature')->nullable()->after('photo_cutout');
            $table->unsignedSmallInteger('portrait_scale')->default(100)->change();
        });

        DB::table('employees')
            ->whereNull('verification_token')
            ->orderBy('id')
            ->eachById(function (object $employee): void {
                DB::table('employees')
                    ->where('id', $employee->id)
                    ->update(['verification_token' => Str::random(48)]);
            });

        DB::table('employees')
            ->where('portrait_scale', '<', 70)
            ->update(['portrait_scale' => 100]);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['verification_token']);
            $table->dropColumn(['verification_token', 'photo_card', 'authorized_signature']);
            $table->unsignedSmallInteger('portrait_scale')->default(58)->change();
        });
    }
};
