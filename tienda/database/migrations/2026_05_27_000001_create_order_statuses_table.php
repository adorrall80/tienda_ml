<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('nombre', 80);
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        $orden = 1;
        foreach (Order::ESTADOS as $slug => $nombre) {
            DB::table('order_statuses')->insert([
                'slug' => $slug,
                'nombre' => $nombre,
                'orden' => $orden++,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
