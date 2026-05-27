<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCondition;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductConditionController extends Controller
{
    public function index(): View
    {
        $conditions = ProductCondition::query()
            ->withCount('products')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        $selectedCondition = null;
        $associatedProducts = collect();

        if (request()->filled('estado')) {
            $selectedCondition = ProductCondition::query()
                ->whereKey(request('estado'))
                ->first();

            if ($selectedCondition) {
                $associatedProducts = $selectedCondition->products()
                    ->with(['tienda', 'category', 'productCondition'])
                    ->orderBy('nombre')
                    ->get();
            }
        }

        return view('admin.mantenedores.product-conditions', compact('conditions', 'selectedCondition', 'associatedProducts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', new NoReservedAttackWords],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['nombre']);
        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        ProductCondition::create($data);

        return back()->with('success', 'Estado de producto creado.');
    }

    public function update(Request $request, ProductCondition $condition): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', new NoReservedAttackWords],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        if ($condition->nombre !== $data['nombre']) {
            $data['slug'] = $this->uniqueSlug($data['nombre'], $condition);
        }

        $condition->update($data);

        return back()->with('success', 'Estado de producto actualizado.');
    }

    public function destroy(ProductCondition $condition): RedirectResponse
    {
        if ($condition->products()->exists()) {
            return back()->withErrors([
                'condition' => 'No se puede eliminar un estado de producto que ya esta ocupado por productos.',
            ]);
        }

        $condition->delete();

        return back()->with('success', 'Estado de producto eliminado.');
    }

    private function uniqueSlug(string $name, ?ProductCondition $ignore = null): string
    {
        $base = Str::slug($name) ?: 'estado-producto';
        $slug = $base;
        $counter = 1;

        while (ProductCondition::where('slug', $slug)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
