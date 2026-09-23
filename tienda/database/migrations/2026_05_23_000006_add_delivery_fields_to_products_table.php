<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('retiro_en_domicilio')->default(false)->after('envio_gratis');
            $table->boolean('delivery')->default(false)->after('retiro_en_domicilio');
            $table->boolean('envio_courier')->default(false)->after('delivery');
            $table->unsignedBigInteger('costo_envio')->nullable()->after('envio_courier');
            $table->string('tiempo_entrega', 120)->nullable()->after('costo_envio');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'retiro_en_domicilio',
                'delivery',
                'envio_courier',
                'costo_envio',
                'tiempo_entrega',
            ]);
        });
    }
};
