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
            $table->unsignedTinyInteger('estado_publicacion_id')
                ->default(Product::PUBLICACION_ACTIVO)
                ->after('estado_id');
        });

        DB::table('products')
            ->where('activo', false)
            ->update(['estado_publicacion_id' => Product::PUBLICACION_PAUSADO]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('estado_publicacion_id');
        });
    }
};
