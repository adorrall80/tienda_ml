<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function index(): View
    {
        $statuses = OrderStatus::query()
            ->withCount('orders')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
        $selectedStatus = null;
        $associatedOrders = collect();

        if (request()->filled('estado')) {
            $selectedStatus = OrderStatus::where('slug', request('estado'))->first();

            if ($selectedStatus) {
                $associatedOrders = $selectedStatus->orders()
                    ->with('user')
                    ->latest()
                    ->get();
            }
        }

        return view('admin.mantenedores.order-statuses', compact('statuses', 'selectedStatus', 'associatedOrders'));
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

        OrderStatus::create($data);

        return back()->with('success', 'Estado de pedido creado.');
    }

    public function update(Request $request, OrderStatus $status): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', new NoReservedAttackWords],
            'orden' => ['nullable', 'integer', 'min:0'],
            'activo' => ['boolean'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['activo'] = $request->boolean('activo');

        if ($status->nombre !== $data['nombre'] && ! $status->orders()->exists()) {
            $data['slug'] = $this->uniqueSlug($data['nombre'], $status);
        }

        $status->update($data);

        return back()->with('success', 'Estado de pedido actualizado.');
    }

    public function destroy(OrderStatus $status): RedirectResponse
    {
        $hasHistory = DB::table('order_status_histories')
            ->where('estado_anterior', $status->slug)
            ->orWhere('estado_nuevo', $status->slug)
            ->exists();

        if ($status->orders()->exists() || $hasHistory) {
            return back()->withErrors([
                'status' => 'No se puede eliminar un estado de pedido que ya esta ocupado por pedidos o historial.',
            ]);
        }

        $status->delete();

        return back()->with('success', 'Estado de pedido eliminado.');
    }

    private function uniqueSlug(string $name, ?OrderStatus $ignore = null): string
    {
        $base = Str::slug($name) ?: 'estado-pedido';
        $slug = $base;
        $counter = 1;

        while (OrderStatus::where('slug', $slug)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
