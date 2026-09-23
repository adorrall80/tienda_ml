<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DeliveryType;
use App\Models\Product;
use App\Models\ProductCondition;
use App\Models\ProductImage;
use App\Models\Tienda;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    private function normalizePriceInputs(Request $request): void
    {
        foreach (['precio', 'precio_oferta', 'costo_envio'] as $field) {
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

    private function storeGalleryImages(Request $request, Product $producto): void
    {
        if (! $request->hasFile('galeria_archivos')) {
            return;
        }

        $orden = (int) $producto->images()->max('orden');

        foreach ($request->file('galeria_archivos') as $file) {
            $producto->images()->create([
                'imagen' => Storage::url($file->store('products/gallery', 'public')),
                'orden' => ++$orden,
            ]);
        }
    }

    private function syncProductAttributes(Product $producto, array $attributes = []): void
    {
        $rows = collect($attributes)
            ->map(fn ($row) => [
                'nombre' => trim((string) ($row['nombre'] ?? '')),
                'valor' => trim((string) ($row['valor'] ?? '')),
            ])
            ->filter(fn ($row) => $row['nombre'] !== '' && $row['valor'] !== '')
            ->values();

        $producto->productAttributes()->delete();

        $rows->each(function ($row, int $index) use ($producto) {
            $producto->productAttributes()->create([
                'nombre' => $row['nombre'],
                'valor' => $row['valor'],
                'orden' => $index + 1,
            ]);
        });
    }

    private function syncDeliveryTypes(Product $producto, array $deliveryTypeIds): void
    {
        $ids = DeliveryType::query()
            ->whereIn('id', $deliveryTypeIds)
            ->pluck('id')
            ->all();

        $producto->deliveryTypes()->sync($ids);

        $slugs = DeliveryType::whereIn('id', $ids)->pluck('slug')->all();
        $producto->update([
            'retiro_en_domicilio' => in_array('retiro-en-domicilio', $slugs, true),
            'delivery' => in_array('delivery-propio', $slugs, true),
            'envio_courier' => in_array('envio-por-courier', $slugs, true),
        ]);
    }

    private function deliveryTypeIdsFromRequest(Request $request, array $validated): array
    {
        if (! empty($validated['delivery_type_ids'])) {
            return $validated['delivery_type_ids'];
        }

        $slugs = collect([
            $request->boolean('retiro_en_domicilio') ? 'retiro-en-domicilio' : null,
            $request->boolean('delivery') ? 'delivery-propio' : null,
            $request->boolean('envio_courier') ? 'envio-por-courier' : null,
        ])->filter()->all();

        return DeliveryType::whereIn('slug', $slugs)->pluck('id')->all();
    }

    public function index(Request $request)
    {
        $search = $request->input('q');
        $perPage = in_array((int) $request->query('per_page', 20), [10, 20, 50], true)
            ? (int) $request->query('per_page', 20)
            : 20;

        $productos = Product::with(['category', 'tienda', 'productCondition'])
            ->withCount('favorites')
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
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.productos.index', compact('productos', 'search', 'perPage'));
    }

    public function create()
    {
        $categorias = Category::orderBy('orden')->get();
        $tiendas    = Tienda::orderBy('nombre')->get();
        $deliveryTypes = DeliveryType::activos()->orderBy('orden')->orderBy('nombre')->get();
        $productConditions = ProductCondition::activos()->orderBy('orden')->orderBy('nombre')->get();

        return view('admin.productos.create', compact('categorias', 'tiendas', 'deliveryTypes', 'productConditions'));
    }

    public function store(Request $request)
    {
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);
        $request->merge([
            'estado_revision_id' => $request->input('estado_revision_id', Product::REVISION_APROBADO),
        ]);

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
            'galeria_archivos' => 'nullable|array',
            'galeria_archivos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'envio_gratis'    => 'boolean',
            'delivery_type_ids' => 'nullable|array',
            'delivery_type_ids.*' => 'integer|exists:delivery_types,id',
            'costo_envio' => 'nullable|integer|min:0',
            'tiempo_entrega' => ['nullable', 'string', 'max:120', new NoReservedAttackWords],
            'destacado'       => 'boolean',
            'estado_id'       => ['required', 'integer', 'exists:product_conditions,id'],
            'estado_publicacion_id' => ['required', 'integer', Rule::in(array_keys(Product::ESTADOS_PUBLICACION))],
            'estado_revision_id' => ['required', 'integer', Rule::in(array_keys(Product::ESTADOS_REVISION))],
            'motivo_rechazo' => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'bloqueado' => 'boolean',
            'motivo_bloqueo' => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'atributos' => 'nullable|array',
            'atributos.*.nombre' => ['nullable', 'string', 'max:100', new NoReservedAttackWords],
            'atributos.*.valor' => ['nullable', 'string', 'max:255', new NoReservedAttackWords],
        ]);

        $data['slug']         = Str::slug($data['nombre']);
        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $deliveryTypeIds = $this->deliveryTypeIdsFromRequest($request, $data);
        $data['retiro_en_domicilio'] = false;
        $data['delivery'] = false;
        $data['envio_courier'] = false;
        $data['destacado']    = $request->boolean('destacado');
        $data['bloqueado'] = $request->boolean('bloqueado');
        $data['activo']       = (int) $data['estado_publicacion_id'] === Product::PUBLICACION_ACTIVO;
        if ((int) $data['estado_revision_id'] !== Product::REVISION_RECHAZADO) {
            $data['motivo_rechazo'] = null;
        }
        if (! $data['bloqueado']) {
            $data['motivo_bloqueo'] = null;
        }
        $data['fecha_publicacion'] = $data['activo'] ? now() : null;
        if (($data['precio_oferta'] ?? null) !== null && ($data['precio'] ?? null) !== null && $data['precio_oferta'] > $data['precio']) {
            return back()->withErrors(['precio_oferta' => 'El precio oferta no puede ser mayor al precio normal.'])->withInput();
        }
        if ($request->hasFile('imagen_archivo')) {
            $data['imagen'] = Storage::url($request->file('imagen_archivo')->store('products', 'public'));
        }
        $atributos = $data['atributos'] ?? [];
        unset($data['imagen_archivo'], $data['galeria_archivos'], $data['atributos'], $data['delivery_type_ids']);
        // Garantizar slug único
        $base = $data['slug'];
        $n    = 1;
        while (Product::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $n++;
        }

        $producto = Product::create($data);
        $this->storeGalleryImages($request, $producto);
        $this->syncDeliveryTypes($producto, $deliveryTypeIds);
        $this->syncProductAttributes($producto, $atributos);

        return match ($request->input('guardar_accion')) {
            'nuevo' => redirect()->route('admin.productos.create')->with('success', 'Producto creado. Puedes agregar otro.'),
            'listado' => redirect()->route('admin.productos.index')->with('success', 'Producto creado.'),
            default => redirect()->route('admin.productos.edit', $producto)->with('success', 'Producto creado.'),
        };
    }

    public function edit(Product $producto)
    {
        $categorias = Category::orderBy('orden')->get();
        $tiendas    = Tienda::orderBy('nombre')->get();
        $deliveryTypes = DeliveryType::activos()->orderBy('orden')->orderBy('nombre')->get();
        $productConditions = ProductCondition::activos()->orderBy('orden')->orderBy('nombre')->get();
        $producto->loadMissing('deliveryTypes');

        return view('admin.productos.edit', compact('producto', 'categorias', 'tiendas', 'deliveryTypes', 'productConditions'));
    }

    public function preview(Product $producto)
    {
        $producto->load(['tags', 'category', 'images', 'tienda', 'productAttributes', 'deliveryTypes', 'productCondition']);
        $producto->loadCount('favorites');

        $isFavorited = auth()->user()
            ? auth()->user()->favorites()->where('product_id', $producto->id)->exists()
            : false;

        $relacionados = Product::publicados()
            ->with(['tags', 'tienda', 'productCondition'])
            ->withCount('favorites')
            ->where('category_id', $producto->category_id)
            ->where('id', '!=', $producto->id)
            ->take(6)
            ->get();

        return view('shop.producto', compact('producto', 'relacionados', 'isFavorited'));
    }

    public function update(Request $request, Product $producto)
    {
        $this->normalizePriceInputs($request);
        $this->normalizeDescriptionHtml($request);
        $request->merge([
            'estado_revision_id' => $request->input('estado_revision_id', $producto->estado_revision_id ?? Product::REVISION_APROBADO),
        ]);

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
            'galeria_archivos' => 'nullable|array',
            'galeria_archivos.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'envio_gratis'    => 'boolean',
            'delivery_type_ids' => 'nullable|array',
            'delivery_type_ids.*' => 'integer|exists:delivery_types,id',
            'costo_envio' => 'nullable|integer|min:0',
            'tiempo_entrega' => ['nullable', 'string', 'max:120', new NoReservedAttackWords],
            'destacado'       => 'boolean',
            'estado_id'       => ['required', 'integer', 'exists:product_conditions,id'],
            'estado_publicacion_id' => ['required', 'integer', Rule::in(array_keys(Product::ESTADOS_PUBLICACION))],
            'estado_revision_id' => ['required', 'integer', Rule::in(array_keys(Product::ESTADOS_REVISION))],
            'motivo_rechazo' => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'bloqueado' => 'boolean',
            'motivo_bloqueo' => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'atributos' => 'nullable|array',
            'atributos.*.nombre' => ['nullable', 'string', 'max:100', new NoReservedAttackWords],
            'atributos.*.valor' => ['nullable', 'string', 'max:255', new NoReservedAttackWords],
        ]);

        $data['envio_gratis'] = $request->boolean('envio_gratis');
        $deliveryTypeIds = $this->deliveryTypeIdsFromRequest($request, $data);
        $data['retiro_en_domicilio'] = false;
        $data['delivery'] = false;
        $data['envio_courier'] = false;
        $data['destacado']    = $request->boolean('destacado');
        $data['bloqueado'] = $request->boolean('bloqueado');
        $data['activo']       = (int) $data['estado_publicacion_id'] === Product::PUBLICACION_ACTIVO;
        if ((int) $data['estado_revision_id'] !== Product::REVISION_RECHAZADO) {
            $data['motivo_rechazo'] = null;
        }
        if (! $data['bloqueado']) {
            $data['motivo_bloqueo'] = null;
        }
        if ($data['activo'] && ! $producto->fecha_publicacion) {
            $data['fecha_publicacion'] = now();
        }
        if (($data['precio_oferta'] ?? null) !== null && ($data['precio'] ?? null) !== null && $data['precio_oferta'] > $data['precio']) {
            return back()->withErrors(['precio_oferta' => 'El precio oferta no puede ser mayor al precio normal.'])->withInput();
        }
        if ($request->hasFile('imagen_archivo')) {
            $data['imagen'] = Storage::url($request->file('imagen_archivo')->store('products', 'public'));
        }
        $atributos = $data['atributos'] ?? [];
        unset($data['imagen_archivo'], $data['galeria_archivos'], $data['atributos'], $data['delivery_type_ids']);
        $producto->update($data);
        $this->storeGalleryImages($request, $producto);
        $this->syncDeliveryTypes($producto, $deliveryTypeIds);
        $this->syncProductAttributes($producto, $atributos);

        return match ($request->input('guardar_accion')) {
            'nuevo' => redirect()->route('admin.productos.create')->with('success', 'Producto actualizado. Puedes agregar otro.'),
            'listado' => redirect()->route('admin.productos.index')->with('success', 'Producto actualizado.'),
            default => redirect()->route('admin.productos.edit', $producto)->with('success', 'Producto actualizado.'),
        };
    }

    public function destroy(Product $producto)
    {
        $producto->delete();

        return back()->with('success', 'Producto eliminado.');
    }

    public function toggle(Product $producto)
    {
        $nuevoEstado = (int) $producto->estado_publicacion_id === Product::PUBLICACION_ACTIVO
            ? Product::PUBLICACION_PAUSADO
            : Product::PUBLICACION_ACTIVO;

        $producto->update([
            'estado_publicacion_id' => $nuevoEstado,
            'activo' => $nuevoEstado === Product::PUBLICACION_ACTIVO,
            'fecha_publicacion' => $nuevoEstado === Product::PUBLICACION_ACTIVO && ! $producto->fecha_publicacion
                ? now()
                : $producto->fecha_publicacion,
        ]);

        return back()->with('success', $producto->activo ? 'Producto activado.' : 'Producto pausado.');
    }

    public function destroyImage(Product $producto, ProductImage $image)
    {
        abort_unless($image->product_id === $producto->id, 404);

        if (str_starts_with($image->imagen, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $image->imagen));
        }

        $image->delete();

        return back()->with('success', 'Imagen de galería eliminada.');
    }

    public function moveImage(Request $request, Product $producto, ProductImage $image)
    {
        abort_unless($image->product_id === $producto->id, 404);

        $direction = $request->input('direction');
        abort_unless(in_array($direction, ['up', 'down'], true), 422);

        $neighbor = $producto->images()
            ->when(
                $direction === 'up',
                fn ($query) => $query->where('orden', '<', $image->orden)->orderByDesc('orden'),
                fn ($query) => $query->where('orden', '>', $image->orden)->orderBy('orden')
            )
            ->first();

        if ($neighbor) {
            $currentOrder = $image->orden;
            $image->update(['orden' => $neighbor->orden]);
            $neighbor->update(['orden' => $currentOrder]);
        }

        return back()->with('success', 'Orden de galería actualizado.');
    }
}
