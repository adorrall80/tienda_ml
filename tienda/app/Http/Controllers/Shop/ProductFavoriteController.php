<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductFavoriteController extends Controller
{
    public function toggle(Request $request, Product $producto): RedirectResponse
    {
        abort_unless(
            $producto->activo
            && $producto->estado_publicacion_id === Product::PUBLICACION_ACTIVO
            && $producto->tienda?->activa,
            404
        );

        $favorite = $request->user()
            ->favorites()
            ->where('product_id', $producto->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'Producto quitado de favoritos.');
        }

        $request->user()->favorites()->create([
            'product_id' => $producto->id,
        ]);

        return back()->with('success', 'Producto guardado en favoritos.');
    }
}
