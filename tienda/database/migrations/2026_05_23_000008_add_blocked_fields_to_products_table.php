<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('bloqueado')->default(false)->after('motivo_rechazo');
            $table->string('motivo_bloqueo', 500)->nullable()->after('bloqueado');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['bloqueado', 'motivo_bloqueo']);
        });
    }
};
