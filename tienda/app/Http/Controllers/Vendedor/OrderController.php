<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $tienda = $request->user()->tienda;

        if (! $tienda) {
            return redirect()
                ->route('vendedor.tienda.create')
                ->with('warning', 'Crea tu tienda para recibir pedidos.');
        }

        $orders = Order::query()
            ->whereHas('items', fn ($query) => $query->where('tienda_id', $tienda->id))
            ->with(['items' => fn ($query) => $query->where('tienda_id', $tienda->id), 'orderStatus'])
            ->latest()
            ->paginate(15);
        $storeTotalAll = OrderItem::query()
            ->where('tienda_id', $tienda->id)
            ->sum('total');

        return view('vendedor.pedidos.index', compact('tienda', 'orders', 'storeTotalAll'));
    }

    public function show(Request $request, Order $order): View
    {
        $tienda = $request->user()->tienda;

        abort_if(! $tienda, 404);

        $order->load([
            'items' => fn ($query) => $query->where('tienda_id', $tienda->id)->with('product'),
            'user',
            'statusHistories.user',
            'internalNotes' => fn ($query) => $query
                ->where(function ($notes) use ($tienda) {
                    $notes->whereNull('tienda_id')->orWhere('tienda_id', $tienda->id);
                })
                ->with('user'),
            'orderStatus',
        ]);

        abort_if($order->items->isEmpty(), 404);

        $storeTotal = $order->items->sum('total');
        $ordersCount = Order::query()
            ->whereHas('items', fn ($query) => $query->where('tienda_id', $tienda->id))
            ->count();
        $storeTotalAll = OrderItem::query()
            ->where('tienda_id', $tienda->id)
            ->sum('total');
        $orderStatuses = Order::statusOptions();

        return view('vendedor.pedidos.show', compact('tienda', 'order', 'storeTotal', 'ordersCount', 'storeTotalAll', 'orderStatuses'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $tienda = $request->user()->tienda;

        abort_if(! $tienda, 404);
        abort_unless($order->items()->where('tienda_id', $tienda->id)->exists(), 404);

        $data = $request->validate([
            'estado' => ['required', Rule::in(array_keys(Order::statusOptions()))],
        ]);

        $order->recordStatusChange($request->user(), $data['estado'], 'vendedor');

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    public function storeNote(Request $request, Order $order): RedirectResponse
    {
        $tienda = $request->user()->tienda;

        abort_if(! $tienda, 404);
        abort_unless($order->items()->where('tienda_id', $tienda->id)->exists(), 404);

        $data = $request->validate([
            'nota' => ['required', 'string', 'max:1000', new NoReservedAttackWords],
        ]);

        $order->internalNotes()->create([
            'user_id' => $request->user()->id,
            'tienda_id' => $tienda->id,
            'actor' => 'vendedor',
            'nota' => $data['nota'],
        ]);

        return back()->with('success', 'Nota interna agregada.');
    }
}
