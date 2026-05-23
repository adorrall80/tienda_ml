<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductoController extends Controller
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

    private function tienda(Request $request)
    {
        return $request->user()->tienda;
    }

    public function index(Request $request)
    {
        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.panel')->with('warning', 'Primero crea tu tienda.');
        }

        $productos = $tienda->productos()->with('category')->latest()->paginate(20);

        return view('vendedor.productos.index', compact('tienda', 'productos'));
    }

    public function create(Request $request)
    {
        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.panel')->with('warning', 'Primero crea tu tienda.');
        }

        $categorias = Category::orderBy('orden')->get();

        return view('vendedor.productos.create', compact('tienda', 'categorias'));
    }

    public function store(Request $request)
    {
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);

        $tienda = $this->tienda($request);

        if (! $tienda) {
            return redirect()->route('vendedor.panel');
        }

        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255', new NoReservedAttackWords],
            'sku'             => ['nullable', 'string', 'max:50', new NoReservedAttackWords],
            'descripcion_corta' => ['nullable', 'string', 'max:180', new NoReservedAttackWords],
            'descripcion'     => ['nullable', 'string', new NoReservedAttackWords],
            'category_id'     => 'required|exists:categories,id',
            'precio'          => 'nullable|integer|min:0',
            'precio_oferta'   => 'nullable|integer|min:0',
            'stock'           => 'required|integer|min:0',
            'imagen_archivo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'envio_gratis'    => 'boolean',
            'estado'          => 'required|in:nuevo,usado,reacondicionado',
        ]);

        $data['tienda_id']    = $tienda->id;
        $data['activo']       = true;
        $data['envio_gratis'] = $request->boolean('envio_gratis');
        if (($data['precio_oferta'] ?? null) !== null && ($data['precio'] ?? null) !== null && $data['precio_oferta'] > $data['precio']) {
            return back()->withErrors(['precio_oferta' => 'El precio oferta no puede ser mayor al precio normal.'])->withInput();
        }
        if ($request->hasFile('imagen_archivo')) {
            $data['imagen'] = Storage::url($request->file('imagen_archivo')->store('products', 'public'));
        }
        unset($data['imagen_archivo']);
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
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);

        $tienda = $this->tienda($request);

        abort_if($producto->tienda_id !== $tienda?->id, 403);

        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:255', new NoReservedAttackWords],
            'sku'             => ['nullable', 'string', 'max:50', new NoReservedAttackWords],
            'descripcion_corta' => ['nullable', 'string', 'max:180', new NoReservedAttackWords],
            'descripcion'     => ['nullable', 'string', new NoReservedAttackWords],
            'category_id'     => 'required|exists:categories,id',
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
