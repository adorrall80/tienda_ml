<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('fecha_publicacion')->nullable()->after('estado_publicacion_id');
            $table->unsignedBigInteger('visitas')->default(0)->after('fecha_publicacion');
        });

        DB::table('products')
            ->where('estado_publicacion_id', Product::PUBLICACION_ACTIVO)
            ->whereNull('fecha_publicacion')
            ->update(['fecha_publicacion' => now()]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['fecha_publicacion', 'visitas']);
        });
    }
};
