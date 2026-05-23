<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(Request $request): View
    {
        return view('shop.checkout', [
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'cliente_telefono' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string', 'max:1000', new NoReservedAttackWords],
            'cart_payload' => ['required', 'string'],
        ]);

        $items = json_decode($data['cart_payload'], true);

        Validator::make(['items' => $items], [
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ])->validate();

        $order = DB::transaction(function () use ($request, $data, $items) {
            $quantities = collect($items)
                ->groupBy(fn($item) => (int) $item['id'])
                ->map(fn($rows) => $rows->sum(fn($row) => (int) $row['qty']));

            $products = Product::publicados()
                ->with('tienda')
                ->whereIn('id', $quantities->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                return back()->withErrors(['cart' => 'Hay productos no disponibles en tu carrito.'])->withInput();
            }

            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                if ($product->stock < $qty) {
                    return back()->withErrors([
                        'cart' => "No hay stock suficiente para {$product->nombre}.",
                    ])->withInput();
                }
            }

            $subtotal = $products->reduce(function (int $sum, Product $product) use ($quantities) {
                return $sum + ((int) $product->precio_final * (int) $quantities[$product->id]);
            }, 0);

            $order = Order::create([
                'numero' => $this->makeOrderNumber(),
                'user_id' => $request->user()->id,
                'cliente_nombre' => $request->user()->name,
                'cliente_email' => $request->user()->email,
                'cliente_telefono' => $data['cliente_telefono'] ?? null,
                'direccion' => 'No aplica',
                'comuna' => 'No aplica',
                'ciudad' => 'No aplica',
                'notas' => $data['notas'] ?? null,
                'subtotal' => $subtotal,
                'envio' => 0,
                'total' => $subtotal,
                'estado' => 'pendiente',
                'estado_pago' => 'pendiente',
            ]);

            foreach ($products as $product) {
                $qty = (int) $quantities[$product->id];
                $unitPrice = (int) $product->precio_final;

                $order->items()->create([
                    'product_id' => $product->id,
                    'tienda_id' => $product->tienda_id,
                    'producto_nombre' => $product->nombre,
                    'producto_slug' => $product->slug,
                    'tienda_nombre' => $product->tienda?->nombre,
                    'cantidad' => $qty,
                    'precio_unitario' => $unitPrice,
                    'total' => $unitPrice * $qty,
                ]);

                $product->decrement('stock', $qty);
            }

            return $order;
        });

        if ($order instanceof RedirectResponse) {
            return $order;
        }

        return redirect()->route('checkout.confirmacion', $order);
    }

    public function confirmation(Order $order): View
    {
        abort_unless($order->user_id === request()->user()->id, 404);

        $order->load('items.tienda.user');

        return view('shop.checkout-confirmacion', compact('order'));
    }

    private function makeOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Order::where('numero', $number)->exists());

        return $number;
    }
}
