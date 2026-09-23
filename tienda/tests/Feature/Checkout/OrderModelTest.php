<?php

namespace Tests\Feature\Checkout;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_user_and_has_items(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'numero' => 'ORD-TEST-001',
            'user_id' => $user->id,
            'cliente_nombre' => 'Cliente Test',
            'cliente_email' => 'cliente@example.com',
            'direccion' => 'Calle 123',
            'comuna' => 'Santiago',
            'ciudad' => 'Santiago',
            'subtotal' => 10000,
            'envio' => 0,
            'total' => 10000,
            'estado' => 'pendiente',
            'estado_pago' => 'pendiente',
        ]);

        $this->assertTrue($order->user->is($user));
        $this->assertCount(0, $order->items);
    }

    public function test_order_item_keeps_product_and_store_snapshot(): void
    {
        $category = Category::create([
            'nombre' => 'Categoria Orden Test',
            'slug' => 'categoria-orden-test',
            'activo' => true,
        ]);
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = Tienda::create([
            'user_id' => $vendor->id,
            'nombre' => 'Tienda Orden Test',
            'slug' => 'tienda-orden-test',
            'activa' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Orden Test',
            'slug' => 'producto-orden-test',
            'precio' => 15000,
            'stock' => 5,
            'imagen' => 'https://example.com/product.jpg',
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
        ]);
        $order = Order::create([
            'numero' => 'ORD-TEST-002',
            'cliente_nombre' => 'Cliente Test',
            'cliente_email' => 'cliente@example.com',
            'direccion' => 'Calle 123',
            'comuna' => 'Santiago',
            'ciudad' => 'Santiago',
            'subtotal' => 30000,
            'total' => 30000,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'tienda_id' => $store->id,
            'producto_nombre' => $product->nombre,
            'producto_slug' => $product->slug,
            'tienda_nombre' => $store->nombre,
            'cantidad' => 2,
            'precio_unitario' => 15000,
            'total' => 30000,
        ]);

        $this->assertTrue($item->order->is($order));
        $this->assertTrue($item->product->is($product));
        $this->assertTrue($item->tienda->is($store));
        $this->assertSame('Producto Orden Test', $item->producto_nombre);
        $this->assertSame('Tienda Orden Test', $item->tienda_nombre);
    }
}
