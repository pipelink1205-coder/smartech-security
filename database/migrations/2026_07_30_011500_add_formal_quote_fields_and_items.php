<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('quote_number', 32)->nullable()->unique()->after('id');
            $table->string('project_title')->nullable()->after('service');
            $table->string('client_address')->nullable()->after('zone');
            $table->date('valid_until')->nullable()->after('client_address');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('price_max');
            $table->text('terms')->nullable()->after('notes');
        });

        // Ampliar estados sin romper MySQL enum / SQLite.
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE quotes MODIFY COLUMN status VARCHAR(32) NOT NULL DEFAULT 'new'");
        } else {
            Schema::table('quotes', function (Blueprint $table) {
                $table->string('status', 32)->default('new')->change();
            });
        }

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('code', 80)->nullable();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quote_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->nullable()->index();
            $table->string('description');
            $table->decimal('default_unit_price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quote_catalog_items');

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'quote_number',
                'project_title',
                'client_address',
                'valid_until',
                'tax_percent',
                'terms',
            ]);
        });
    }
};
