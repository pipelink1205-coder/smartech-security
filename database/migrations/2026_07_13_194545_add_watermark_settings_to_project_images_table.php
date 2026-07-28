<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->string('original_path')->nullable()->after('path');
            $table->string('wm_size', 10)->default('md')->after('is_cover');
            $table->string('wm_position', 20)->default('center')->after('wm_size');
            $table->decimal('wm_opacity', 3, 2)->default(0.22)->after('wm_position');
        });
    }

    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn(['original_path', 'wm_size', 'wm_position', 'wm_opacity']);
        });
    }
};
