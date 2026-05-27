<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_types', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('slug', 80)->unique();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_type_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_id', 'delivery_type_id']);
        });

        $now = now();
        $types = [
            ['nombre' => 'Retiro en domicilio', 'slug' => 'retiro-en-domicilio', 'orden' => 1, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Delivery propio', 'slug' => 'delivery-propio', 'orden' => 2, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Envio por courier', 'slug' => 'envio-por-courier', 'orden' => 3, 'activo' => true, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('delivery_types')->insert($types);

        $typeIds = DB::table('delivery_types')->pluck('id', 'slug');
        $products = DB::table('products')
            ->select('id', 'retiro_en_domicilio', 'delivery', 'envio_courier')
            ->get();

        $pivotRows = [];
        foreach ($products as $product) {
            if ($product->retiro_en_domicilio && isset($typeIds['retiro-en-domicilio'])) {
                $pivotRows[] = ['product_id' => $product->id, 'delivery_type_id' => $typeIds['retiro-en-domicilio'], 'created_at' => $now, 'updated_at' => $now];
            }
            if ($product->delivery && isset($typeIds['delivery-propio'])) {
                $pivotRows[] = ['product_id' => $product->id, 'delivery_type_id' => $typeIds['delivery-propio'], 'created_at' => $now, 'updated_at' => $now];
            }
            if ($product->envio_courier && isset($typeIds['envio-por-courier'])) {
                $pivotRows[] = ['product_id' => $product->id, 'delivery_type_id' => $typeIds['envio-por-courier'], 'created_at' => $now, 'updated_at' => $now];
            }
        }

        foreach (array_chunk($pivotRows, 500) as $chunk) {
            DB::table('delivery_type_product')->insert($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_type_product');
        Schema::dropIfExists('delivery_types');
    }
};
