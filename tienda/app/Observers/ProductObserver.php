<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        if ($product->stock != 0) {
            $product->stockMovements()->create([
                'user_id' => auth()->id(),
                'tipo' => 'ajuste_inicial',
                'cantidad' => $product->stock,
                'notas' => 'Inventario inicial al crear el producto',
            ]);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->isDirty('stock')) {
            $oldStock = (int) $product->getOriginal('stock');
            $newStock = (int) $product->stock;
            $diff = $newStock - $oldStock;

            if ($diff !== 0) {
                // If it's modified outside of an order context (like admin panel)
                $product->stockMovements()->create([
                    'user_id' => auth()->id(),
                    'tipo' => 'ajuste_manual',
                    'cantidad' => $diff,
                    'notas' => "Ajuste manual de $oldStock a $newStock",
                ]);
            }
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
