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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cliente_nombre');
            $table->string('cliente_email');
            $table->string('cliente_telefono')->nullable();
            $table->string('direccion');
            $table->string('comuna');
            $table->string('ciudad');
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('envio')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('estado')->default('pendiente');
            $table->string('estado_pago')->default('pendiente');
            $table->timestamps();

            $table->index(['user_id', 'estado']);
            $table->index('estado_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
