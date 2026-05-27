<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('estado', 'enviado')
            ->update(['estado' => 'preparado']);

        DB::table('order_status_histories')
            ->where('estado_anterior', 'enviado')
            ->update(['estado_anterior' => 'preparado']);

        DB::table('order_status_histories')
            ->where('estado_nuevo', 'enviado')
            ->update(['estado_nuevo' => 'preparado']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('estado', 'preparado')
            ->update(['estado' => 'enviado']);

        DB::table('order_status_histories')
            ->where('estado_anterior', 'preparado')
            ->update(['estado_anterior' => 'enviado']);

        DB::table('order_status_histories')
            ->where('estado_nuevo', 'preparado')
            ->update(['estado_nuevo' => 'enviado']);
    }
};
