<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->decimal('wm_x', 5, 2)->nullable()->after('wm_position');
            $table->decimal('wm_y', 5, 2)->nullable()->after('wm_x');
        });
    }

    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn(['wm_x', 'wm_y']);
        });
    }
};
