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
            $table->unsignedTinyInteger('estado_revision_id')
                ->default(Product::REVISION_APROBADO)
                ->after('estado_publicacion_id');
            $table->string('motivo_rechazo', 500)->nullable()->after('estado_revision_id');
        });

        DB::table('products')->update([
            'estado_revision_id' => Product::REVISION_APROBADO,
        ]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['estado_revision_id', 'motivo_rechazo']);
        });
    }
};
