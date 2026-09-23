<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            $table->boolean('permite_whatsapp')->default(true)->after('contacto_whatsapp');
            $table->boolean('telefono_visible')->default(true)->after('contacto_telefono');
        });
    }

    public function down(): void
    {
        Schema::table('tiendas', function (Blueprint $table) {
            $table->dropColumn(['permite_whatsapp', 'telefono_visible']);
        });
    }
};
