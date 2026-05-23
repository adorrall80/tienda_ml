<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('tienda_id')->nullable()->constrained('tiendas')->nullOnDelete();
            $table->string('producto_nombre');
            $table->string('producto_slug')->nullable();
            $table->string('tienda_nombre')->nullable();
            $table->unsignedInteger('cantidad');
            $table->unsignedBigInteger('precio_unitario');
            $table->unsignedBigInteger('total');
            $table->timestamps();

            $table->index(['order_id', 'tienda_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
