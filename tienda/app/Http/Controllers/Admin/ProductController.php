<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');

        $productos = Product::with(['category', 'tienda'])
            ->when($search, fn($q) => $q->where('nombre', 'like', "%$search%")
                                        ->orWhere('slug', 'like', "%$search%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.productos.index', compact('productos', 'search'));
    }

    public function create()
    {
        $categorias = Category::orderBy('orden')->get();
        $tiendas    = Tienda::orderBy('nombre')->get();

        return view('admin.productos.create', compact('categorias', 'tiendas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'category_id'     => 'required|exists:categories,id',
            'tienda_id'       => 'required|exists:tiendas,id',
            'precio'          => 'nullable|numeric|min:0',
            'precio_original' => 'nullable|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen'          => 'nullable|url|max:500',
            'envio_gratis'    => 'boolean',
            'cuotas'          => 'nullable|integer|min:1',
            'activo'          => 'boolean',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['slug']         = Str::slug($data['nombre']);
        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $data['activo']       = $request->boolean('activo');
        if (empty($data['precio'])) {
            $data['precio_original'] = null;
        } elseif ($data['precio'] == 0) {
            $data['precio_original'] = 0;
        }

        // Garantizar slug único
        $base = $data['slug'];
        $n    = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $n++;
        }

        Product::create($data);

        return redirect()->route('admin.productos.index')->with('success', 'Producto creado.');
    }

    public function edit(Product $producto)
    {
        $categorias = Category::orderBy('orden')->get();
        $tiendas    = Tienda::orderBy('nombre')->get();

        return view('admin.productos.edit', compact('producto', 'categorias', 'tiendas'));
    }

    public function update(Request $request, Product $producto)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'category_id'     => 'required|exists:categories,id',
            'tienda_id'       => 'required|exists:tiendas,id',
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

        return redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $producto)
    {
        $producto->delete();

        return back()->with('success', 'Producto eliminado.');
    }

    public function toggle(Product $producto)
    {
        $producto->update(['activo' => ! $producto->activo]);

        return back()->with('success', $producto->activo ? 'Producto activado.' : 'Producto desactivado.');
    }
}
