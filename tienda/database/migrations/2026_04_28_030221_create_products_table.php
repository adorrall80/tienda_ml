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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('sku', 50)->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedBigInteger('precio');
            $table->unsignedBigInteger('precio_original')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('imagen');
            $table->boolean('envio_gratis')->default(false);
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
