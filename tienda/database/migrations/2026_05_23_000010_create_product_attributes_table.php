<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->string('valor', 255);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
