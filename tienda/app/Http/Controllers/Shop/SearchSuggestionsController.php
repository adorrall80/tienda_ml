<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = mb_substr(trim((string) $request->query('q', '')), 0, 80);

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::publicados()
            ->where(function ($query) use ($term) {
                $query->where('nombre', 'like', '%'.$term.'%')
                    ->orWhere('descripcion_corta', 'like', '%'.$term.'%')
                    ->orWhere('descripcion', 'like', '%'.$term.'%');
            })
            ->orderByDesc('rating_count')
            ->limit(6)
            ->pluck('nombre');

        $categories = Category::activas()
            ->where('nombre', 'like', '%'.$term.'%')
            ->limit(4)
            ->pluck('nombre');

        return response()->json(
            $products
                ->merge($categories)
                ->unique()
                ->take(8)
                ->values()
        );
    }
}
