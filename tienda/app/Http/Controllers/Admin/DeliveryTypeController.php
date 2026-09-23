<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryType;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DeliveryTypeController extends Controller
{
    public function index(): View
    {
        $deliveryTypes = DeliveryType::query()
            ->withCount('products')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        $selectedDeliveryType = null;
        $associatedProducts = collect();

        if (request()->filled('tipo')) {
            $selectedDeliveryType = DeliveryType::query()
                ->whereKey(request('tipo'))
                ->first();

            if ($selectedDeliveryType) {
                $associatedProducts = $selectedDeliveryType->products()
                    ->with(['tienda', 'category', 'productCondition'])
                    ->orderBy('nombre')
                    ->get();
            }
        }

        return view('admin.mantenedores.delivery-types', compact('deliveryTypes', 'selectedDeliveryType', 'associatedProducts'));
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

        DeliveryType::create($data);

        return back()->with('success', 'Tipo de entrega creado.');
    }

    public function update(Request $request, DeliveryType $deliveryType): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', new NoReservedAttackWords],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        if ($deliveryType->nombre !== $data['nombre']) {
            $data['slug'] = $this->uniqueSlug($data['nombre'], $deliveryType);
        }

        $deliveryType->update($data);

        return back()->with('success', 'Tipo de entrega actualizado.');
    }

    public function destroy(DeliveryType $deliveryType): RedirectResponse
    {
        if ($deliveryType->products()->exists()) {
            return back()->withErrors([
                'delivery_type' => 'No se puede eliminar un tipo de entrega que ya esta ocupado por productos.',
            ]);
        }

        $deliveryType->delete();

        return back()->with('success', 'Tipo de entrega eliminado.');
    }

    private function uniqueSlug(string $name, ?DeliveryType $ignore = null): string
    {
        $base = Str::slug($name) ?: 'tipo-entrega';
        $slug = $base;
        $counter = 1;

        while (DeliveryType::where('slug', $slug)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
