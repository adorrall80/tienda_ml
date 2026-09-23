<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('products', 'sku')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 50)->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('products', 'sku')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
