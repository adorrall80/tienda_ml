<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            $table->string('contacto_email')->nullable()->after('descripcion');
            $table->string('contacto_telefono', 50)->nullable()->after('contacto_email');
            $table->string('contacto_whatsapp', 50)->nullable()->after('contacto_telefono');
            $table->string('contacto_direccion')->nullable()->after('contacto_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            $table->dropColumn([
                'contacto_email',
                'contacto_telefono',
                'contacto_whatsapp',
                'contacto_direccion',
            ]);
        });
    }
};
