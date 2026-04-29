<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Category::activas()->raiz()->get();

        $query = Product::activos()->with('tags');

        // Filtro: categoría
        $categoriaActual = null;
        if ($request->filled('cat')) {
            $categoriaActual = Category::where('slug', $request->cat)->first();
            if ($categoriaActual) {
                $query->where('category_id', $categoriaActual->id);
            }
        }

        // Filtro: búsqueda
        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%' . $request->q . '%');
        }

        // Filtro: envío gratis
        if ($request->boolean('envio_gratis')) {
            $query->where('envio_gratis', true);
        }

        // Filtro: rango de precio
        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (int) preg_replace('/[^0-9]/', '', $request->precio_min));
        }
        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (int) preg_replace('/[^0-9]/', '', $request->precio_max));
        }

        // Orden
        match ($request->get('orden', 'relevante')) {
            'precio_asc'  => $query->orderBy('precio'),
            'precio_desc' => $query->orderByDesc('precio'),
            'nuevos'      => $query->latest(),
            'rating'      => $query->orderByDesc('rating')->orderByDesc('rating_count'),
            default       => $query->orderByDesc('rating_count'),
        };

        $productos = $query->paginate(12)->withQueryString();

        $titulo = match (true) {
            $categoriaActual !== null    => $categoriaActual->nombre,
            $request->filled('q')       => 'Resultados para "' . $request->q . '"',
            default                     => 'Todos los productos',
        };

        return view('shop.productos', compact('productos', 'categorias', 'categoriaActual', 'titulo'));
    }

    public function show(string $slug)
    {
        $producto = Product::activos()
            ->with(['tags', 'category', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relacionados = Product::activos()
            ->with('tags')
            ->where('category_id', $producto->category_id)
            ->where('id', '!=', $producto->id)
            ->take(6)
            ->get();

        return view('shop.producto', compact('producto', 'relacionados'));
    }
}
