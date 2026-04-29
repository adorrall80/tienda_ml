<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    private function tienda(Request $request)
    {
        return $request->user()->tienda;
    }

    public function index(Request $request)
    {
        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.dashboard')->with('warning', 'Primero crea tu tienda.');
        }

        $productos = $tienda->productos()->with('category')->latest()->paginate(20);

        return view('vendedor.productos.index', compact('tienda', 'productos'));
    }

    public function create(Request $request)
    {
        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.dashboard')->with('warning', 'Primero crea tu tienda.');
        }

        $categorias = Category::orderBy('orden')->get();

        return view('vendedor.productos.create', compact('tienda', 'categorias'));
    }

    public function store(Request $request)
    {
        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.dashboard');
        }

        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'category_id'     => 'required|exists:categories,id',
            'precio'          => 'nullable|numeric|min:0',
            'precio_original' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen'          => 'nullable|url|max:500',
            'envio_gratis'    => 'boolean',
            'cuotas'          => 'nullable|integer|min:1',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['tienda_id']    = $tienda->id;
        $data['activo']       = true;
        $data['envio_gratis'] = $request->boolean('envio_gratis');
        if (empty($data['precio'])) {
            $data['precio_original'] = null;
        } elseif ($data['precio'] == 0) {
            $data['precio_original'] = 0;
        }

        $base = Str::slug($data['nombre']);
        $data['slug'] = $base;
        $n = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $n++;
        }

        Product::create($data);

        return redirect()->route('vendedor.productos.index')->with('success', 'Producto creado.');
    }

    public function edit(Request $request, Product $producto)
    {
        $tienda = $this->tienda($request);

        abort_if($producto->tienda_id !== $tienda?->id, 403);

        $categorias = Category::orderBy('orden')->get();

        return view('vendedor.productos.edit', compact('tienda', 'producto', 'categorias'));
    }

    public function update(Request $request, Product $producto)
    {
        $tienda = $this->tienda($request);

        abort_if($producto->tienda_id !== $tienda?->id, 403);

        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'category_id'     => 'required|exists:categories,id',
            'precio'          => 'nullable|numeric|min:0',
            'precio_original' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen'          => 'nullable|url|max:500',
            'envio_gratis'    => 'boolean',
            'cuotas'          => 'nullable|integer|min:1',
            'activo'          => 'boolean',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $data['activo']       = $request->boolean('activo');
        if (empty($data['precio'])) {
            $data['precio_original'] = null;
        } elseif ($data['precio'] == 0) {
            $data['precio_original'] = 0;
        }

        $producto->update($data);

        return redirect()->route('vendedor.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Request $request, Product $producto)
    {
        $tienda = $this->tienda($request);

        abort_if($producto->tienda_id !== $tienda?->id, 403);

        $producto->delete();

        return back()->with('success', 'Producto eliminado.');
    }

    public function toggle(Request $request, Product $producto)
    {
        $tienda = $this->tienda($request);

        abort_if($producto->tienda_id !== $tienda?->id, 403);

        $producto->update(['activo' => ! $producto->activo]);

        return back()->with('success', $producto->activo ? 'Producto activado.' : 'Producto desactivado.');
    }
}
