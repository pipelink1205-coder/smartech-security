<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->json('features')->nullable()->after('description');
            $table->string('highlight', 80)->nullable()->after('features');
            $table->string('image')->nullable()->after('icon');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['features', 'highlight', 'image']);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
