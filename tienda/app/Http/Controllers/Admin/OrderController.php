<?php

namespace App\Http\Controllers\Admin;

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
    public function index(): View
    {
        $orders = Order::query()
            ->with('items.tienda')
            ->latest()
            ->paginate(20);
        $ordersCount = Order::count();
        $totalSolicitado = OrderItem::sum('total');

        return view('admin.pedidos.index', compact('orders', 'ordersCount', 'totalSolicitado'));
    }

    public function show(Order $order): View
    {
        $order->load('items.tienda.user', 'user', 'statusHistories.user', 'internalNotes.user');

        $storeGroups = $order->items->groupBy('tienda_id');

        return view('admin.pedidos.show', compact('order', 'storeGroups'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'estado' => ['required', Rule::in(array_keys(Order::ESTADOS))],
        ]);

        $order->recordStatusChange($request->user(), $data['estado'], 'admin');

        return back()->with('success', 'Estado del pedido actualizado.');
    }

    public function storeNote(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'nota' => ['required', 'string', 'max:1000', new NoReservedAttackWords],
        ]);

        $order->internalNotes()->create([
            'user_id' => $request->user()->id,
            'actor' => 'admin',
            'nota' => $data['nota'],
        ]);

        return back()->with('success', 'Nota interna agregada.');
    }
}
