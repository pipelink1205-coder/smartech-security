<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 20)->nullable()->unique();
            $table->string('first_names', 100);
            $table->string('last_names', 100);
            $table->string('document_type', 20)->default('CC');
            $table->text('document_number')->nullable();
            $table->string('position', 120);
            $table->string('area', 120)->nullable();
            $table->string('email', 160)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('photo_original')->nullable();
            $table->string('photo_cutout')->nullable();
            $table->unsignedSmallInteger('portrait_scale')->default(58);
            $table->smallInteger('portrait_x')->default(0);
            $table->smallInteger('portrait_y')->default(0);
            $table->string('status', 20)->default('active');
            $table->date('started_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
