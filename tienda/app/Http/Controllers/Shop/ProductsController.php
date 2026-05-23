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
        $searchTerm = mb_substr(trim((string) $request->query('q', '')), 0, 80);
        $perPage = in_array((int) $request->query('per_page', 20), [10, 20, 50], true)
            ? (int) $request->query('per_page', 20)
            : 20;
        $categorias = Category::activas()->raiz()->get();

        $query = Product::publicados()->with('tags');

        // Filtro: categoría
        $categoriaActual = null;
        if ($request->filled('cat')) {
            $categoriaActual = Category::where('slug', $request->cat)->first();
            if ($categoriaActual) {
                $query->where('category_id', $categoriaActual->id);
            }
        }

        // Filtro: búsqueda
        if ($searchTerm !== '') {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion_corta', 'like', '%' . $searchTerm . '%')
                    ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filtro: envío gratis
        if ($request->boolean('envio_gratis')) {
            $query->where('envio_gratis', true);
        }

        // Filtro: estado del producto
        if ($request->filled('estado') && array_key_exists($request->query('estado'), Product::ESTADOS)) {
            $query->where('estado', $request->query('estado'));
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

        $productos = $query->paginate($perPage)->withQueryString();

        $titulo = match (true) {
            $categoriaActual !== null    => $categoriaActual->nombre,
            $searchTerm !== ''          => 'Resultados para "' . $searchTerm . '"',
            default                     => 'Todos los productos',
        };

        return view('shop.productos', compact('productos', 'categorias', 'categoriaActual', 'titulo', 'perPage'));
    }

    public function show(string $slug)
    {
        $producto = Product::publicados()
            ->with(['tags', 'category', 'images', 'tienda'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relacionados = Product::publicados()
            ->with(['tags', 'tienda'])
            ->where('category_id', $producto->category_id)
            ->where('id', '!=', $producto->id)
            ->take(6)
            ->get();

        return view('shop.producto', compact('producto', 'relacionados'));
    }
}
