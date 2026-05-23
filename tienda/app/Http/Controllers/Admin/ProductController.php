<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tienda;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function normalizePriceInputs(Request $request): void
    {
        foreach (['precio', 'precio_oferta'] as $field) {
            if (! $request->has($field)) {
                continue;
            }

            $value = trim((string) $request->input($field));

            $request->merge([
                $field => $value === '' ? null : str_replace('.', '', $value),
            ]);
        }
    }

    private function normalizeDescriptionHtml(Request $request): void
    {
        if (! $request->has('descripcion')) {
            return;
        }

        $html = trim((string) $request->input('descripcion'));

        $request->merge([
            'descripcion' => $html === '' ? null : $this->sanitizeBasicHtml($html),
        ]);
    }

    private function sanitizeBasicHtml(string $html): string
    {
        $html = preg_replace('/<(script|style|iframe)\b[^>]*>.*?<\/\1>/is', '', $html);
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li>');
        $html = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $html);

        return trim($html);
    }

    public function index(Request $request)
    {
        $search = $request->input('q');

        $productos = Product::with(['category', 'tienda'])
            ->when($search, function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($q) use ($term) {
                    $q->where('nombre', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhere('descripcion_corta', 'like', $term)
                        ->orWhere('descripcion', 'like', $term);
                });
            })
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
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);

        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255', new NoReservedAttackWords],
            'sku'             => ['nullable', 'string', 'max:50', new NoReservedAttackWords],
            'descripcion_corta' => ['nullable', 'string', 'max:180', new NoReservedAttackWords],
            'descripcion'     => ['nullable', 'string', new NoReservedAttackWords],
            'category_id'     => 'required|exists:categories,id',
            'tienda_id'       => 'required|exists:tiendas,id',
            'precio'          => 'nullable|integer|min:0',
            'precio_oferta'   => 'nullable|integer|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen_archivo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'envio_gratis'    => 'boolean',
            'activo'          => 'boolean',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['slug']         = Str::slug($data['nombre']);
        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $data['activo']       = $request->boolean('activo');
        if (($data['precio_oferta'] ?? null) !== null && ($data['precio'] ?? null) !== null && $data['precio_oferta'] > $data['precio']) {
            return back()->withErrors(['precio_oferta' => 'El precio oferta no puede ser mayor al precio normal.'])->withInput();
        }
        if ($request->hasFile('imagen_archivo')) {
            $data['imagen'] = Storage::url($request->file('imagen_archivo')->store('products', 'public'));
        }
        unset($data['imagen_archivo']);
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
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);

        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255', new NoReservedAttackWords],
            'sku'             => ['nullable', 'string', 'max:50', new NoReservedAttackWords],
            'descripcion_corta' => ['nullable', 'string', 'max:180', new NoReservedAttackWords],
            'descripcion'     => ['nullable', 'string', new NoReservedAttackWords],
            'category_id'     => 'required|exists:categories,id',
            'tienda_id'       => 'required|exists:tiendas,id',
            'precio'          => 'nullable|integer|min:0',
            'precio_oferta'   => 'nullable|integer|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen_archivo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'envio_gratis'    => 'boolean',
            'activo'          => 'boolean',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $data['activo']       = $request->boolean('activo');
        if (($data['precio_oferta'] ?? null) !== null && ($data['precio'] ?? null) !== null && $data['precio_oferta'] > $data['precio']) {
            return back()->withErrors(['precio_oferta' => 'El precio oferta no puede ser mayor al precio normal.'])->withInput();
        }
        if ($request->hasFile('imagen_archivo')) {
            $data['imagen'] = Storage::url($request->file('imagen_archivo')->store('products', 'public'));
        }
        unset($data['imagen_archivo']);
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
