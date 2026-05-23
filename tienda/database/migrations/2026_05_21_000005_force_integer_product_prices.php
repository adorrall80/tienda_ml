<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('precio')->nullable()->change();
            $table->unsignedBigInteger('precio_oferta')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('precio', 12, 2)->nullable()->change();
            $table->decimal('precio_oferta', 12, 2)->nullable()->change();
        });
    }
};
