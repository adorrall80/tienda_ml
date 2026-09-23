<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\ProductReport;

class ProductReportController extends Controller
{
    public function store(Request $request, Product $producto)
    {
        $data = $request->validate([
            'report_reason_id' => 'required|exists:report_reasons,id',
            'comentarios' => 'nullable|string|max:1000'
        ]);

        ProductReport::create([
            'product_id' => $producto->id,
            'user_id' => $request->user()->id,
            'report_reason_id' => $data['report_reason_id'],
            'comentarios' => $data['comentarios'],
            'estado' => 'pendiente'
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Gracias, revisaremos este producto.']);
        }

        return back()->with('success', 'Gracias, revisaremos este producto.');
    }
}
