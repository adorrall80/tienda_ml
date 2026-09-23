<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductFavoriteController extends Controller
{
    public function toggle(Request $request, Product $producto)
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

            if ($request->expectsJson()) {
                return response()->json(['status' => 'removed', 'message' => 'Producto quitado de favoritos.']);
            }
            return back()->with('success', 'Producto quitado de favoritos.');
        }

        $request->user()->favorites()->create([
            'product_id' => $producto->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'added', 'message' => 'Producto guardado en favoritos.']);
        }
        return back()->with('success', 'Producto guardado en favoritos.');
    }
}
