<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('service')->nullable();
            $table->text('message')->nullable();
            $table->string('source', 40)->default('link');
            $table->string('page_url', 500)->nullable();
            $table->string('page_title')->nullable();
            $table->string('destination_phone', 20)->nullable();
            $table->unsignedInteger('click_count')->default(1);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('status', 30)->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('phone');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_leads');
    }
};
