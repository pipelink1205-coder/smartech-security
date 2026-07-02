<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('long_description')->nullable()->after('description');
            $table->json('includes')->nullable()->after('long_description');
            $table->json('process_steps')->nullable()->after('includes');
            $table->json('brands')->nullable()->after('process_steps');
            $table->json('standards')->nullable()->after('brands');
            $table->json('tools')->nullable()->after('standards');
            $table->json('faqs')->nullable()->after('tools');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'long_description',
                'includes',
                'process_steps',
                'brands',
                'standards',
                'tools',
                'faqs',
            ]);
        });
    }
};
