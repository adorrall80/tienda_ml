<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DeliveryType;
use App\Models\Product;
use App\Models\ProductCondition;
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
        $productConditions = ProductCondition::activos()->orderBy('orden')->orderBy('nombre')->get();
        $deliveryTypes = DeliveryType::activos()->orderBy('orden')->orderBy('nombre')->get();

        $query = Product::publicados()->with(['tags', 'productCondition', 'deliveryTypes'])->withCount('favorites');

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

        if ($request->filled('delivery_type_id') && $deliveryTypes->contains('id', (int) $request->query('delivery_type_id'))) {
            $query->whereHas('deliveryTypes', fn ($q) => $q->whereKey((int) $request->query('delivery_type_id')));
        }

        // Filtro: estado del producto
        if ($request->filled('estado_id') && $productConditions->contains('id', (int) $request->query('estado_id'))) {
            $query->where('estado_id', (int) $request->query('estado_id'));
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
            'nuevos'      => $query->orderByDesc('fecha_publicacion')->latest(),
            'rating'      => $query->orderByDesc('rating')->orderByDesc('rating_count'),
            default       => $query->orderByDesc('destacado')->orderByDesc('rating_count'),
        };

        $productos = $query->paginate($perPage)->withQueryString();

        $titulo = match (true) {
            $categoriaActual !== null    => $categoriaActual->nombre,
            $searchTerm !== ''          => 'Resultados para "' . $searchTerm . '"',
            default                     => 'Todos los productos',
        };

        return view('shop.productos', compact('productos', 'categorias', 'categoriaActual', 'titulo', 'perPage', 'productConditions', 'deliveryTypes'));
    }

    public function show(string $slug)
    {
        $producto = Product::query()
            ->with(['tags', 'category', 'images', 'tienda', 'productAttributes', 'deliveryTypes', 'productCondition'])
            ->withCount('favorites')
            ->where('slug', $slug)
            ->first();

        if (! $producto) {
            return response()->view('errors.404', [], 404);
        }

        $isPublic = $producto->activo
            && $producto->estado_publicacion_id === Product::PUBLICACION_ACTIVO
            && $producto->estado_revision_id === Product::REVISION_APROBADO
            && ! $producto->bloqueado
            && (bool) $producto->tienda?->activa;

        $user = auth()->user();
        $canPreviewPrivate = $user && (
            (method_exists($user, 'hasRole') && $user->hasRole('admin'))
            || (int) $producto->tienda?->user_id === (int) $user->id
        );

        if (! $isPublic && ! $canPreviewPrivate) {
            return response()->view('errors.404', [], 404);
        }

        $privatePreviewReason = null;
        if (! $isPublic) {
            $privatePreviewReason = $this->privatePreviewReason($producto);
        }

        $visitKey = 'productos_vistos.' . $producto->id;
        if ($isPublic && ! session()->has($visitKey)) {
            $producto->increment('visitas');
            session()->put($visitKey, true);
            $producto->visitas = (int) $producto->visitas;
        }

        $isFavorited = auth()->check()
            ? auth()->user()->favorites()->where('product_id', $producto->id)->exists()
            : false;

        $relacionados = Product::publicados()
            ->with(['tags', 'tienda', 'productCondition'])
            ->withCount('favorites')
            ->where('category_id', $producto->category_id)
            ->where('id', '!=', $producto->id)
            ->take(6)
            ->get();

        return view('shop.producto', compact('producto', 'relacionados', 'isFavorited', 'privatePreviewReason'));
    }

    private function privatePreviewReason(Product $producto): string
    {
        if ($producto->bloqueado) {
            return 'Este producto esta bloqueado por administracion y no es visible para compradores.';
        }

        if (! $producto->activo || $producto->estado_publicacion_id !== Product::PUBLICACION_ACTIVO) {
            return 'Este producto no esta activo/publicado y solo puedes verlo porque pertenece a tu tienda.';
        }

        if ($producto->estado_revision_id !== Product::REVISION_APROBADO) {
            return 'Este producto esta pendiente de revision y todavia no es visible para compradores.';
        }

        if (! $producto->tienda?->activa) {
            return 'La tienda no esta activa, por eso este producto no es visible para compradores.';
        }

        return 'Vista privada del producto.';
    }
}
