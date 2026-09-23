<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('descripcion_corta', 180)->nullable()->after('descripcion');
            $table->unsignedBigInteger('precio_oferta')->nullable()->after('precio');
            $table->dropColumn('precio_original');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['descripcion_corta', 'precio_oferta']);
            $table->unsignedBigInteger('precio_original')->nullable()->after('precio');
        });
    }
};
