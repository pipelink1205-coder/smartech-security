<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->boolean('is_cover')->default(false)->after('sort_order');
        });

        $projectIds = DB::table('project_images')->distinct()->pluck('project_id');

        foreach ($projectIds as $projectId) {
            $coverId = DB::table('project_images')
                ->where('project_id', $projectId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->value('id');

            if ($coverId === null) {
                continue;
            }

            DB::table('project_images')
                ->where('project_id', $projectId)
                ->update(['is_cover' => false]);

            DB::table('project_images')
                ->where('id', $coverId)
                ->update(['is_cover' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('project_images', function (Blueprint $table) {
            $table->dropColumn('is_cover');
        });
    }
};
