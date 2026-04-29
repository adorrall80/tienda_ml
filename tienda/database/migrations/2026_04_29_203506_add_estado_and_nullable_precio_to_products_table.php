<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('precio', 12, 2)->nullable()->change();
            $table->string('estado')->nullable()->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('precio', 12, 2)->nullable(false)->change();
            $table->dropColumn('estado');
        });
    }
};
