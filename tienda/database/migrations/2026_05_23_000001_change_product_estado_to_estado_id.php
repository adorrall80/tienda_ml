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
            $table->unsignedTinyInteger('estado_id')->nullable()->after('activo');
        });

        DB::table('products')->where('estado', 'nuevo')->update(['estado_id' => Product::ESTADO_NUEVO]);
        DB::table('products')->where('estado', 'usado')->update(['estado_id' => Product::ESTADO_USADO]);
        DB::table('products')->where('estado', 'reacondicionado')->update(['estado_id' => Product::ESTADO_REACONDICIONADO]);
        DB::table('products')->whereNull('estado_id')->update(['estado_id' => Product::ESTADO_NUEVO]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('estado')->nullable()->after('activo');
        });

        DB::table('products')->where('estado_id', Product::ESTADO_NUEVO)->update(['estado' => 'nuevo']);
        DB::table('products')->where('estado_id', Product::ESTADO_USADO)->update(['estado' => 'usado']);
        DB::table('products')->where('estado_id', Product::ESTADO_REACONDICIONADO)->update(['estado' => 'reacondicionado']);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('estado_id');
        });
    }
};
