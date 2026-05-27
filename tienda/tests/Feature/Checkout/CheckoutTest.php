<?php

namespace Tests\Feature\Checkout;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Finalizar compra');
    }

    public function test_checkout_page_requires_authentication(): void
    {
        $this->get(route('checkout.create'))
            ->assertRedirect(route('login'));
    }

    public function test_checkout_creates_order_and_decrements_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct(stock: 5, price: 12000);

        $response = $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 2],
            ]));

        $order = Order::first();

        $response->assertRedirect(route('checkout.confirmacion', $order));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'cliente_nombre' => $user->name,
            'cliente_email' => $user->email,
            'direccion' => 'No aplica',
            'subtotal' => 24000,
            'total' => 24000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'cantidad' => 2,
            'precio_unitario' => 12000,
            'total' => 24000,
        ]);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_checkout_links_order_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]));

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'cliente_email' => $user->email,
        ]);
    }

    public function test_checkout_does_not_require_shipping_data_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct();

        $this->actingAs($user)
            ->post(route('checkout.store'), [
                'cart_payload' => json_encode([
                    ['id' => $product->id, 'qty' => 1],
                ]),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'cliente_nombre' => $user->name,
            'cliente_email' => $user->email,
            'direccion' => 'No aplica',
            'comuna' => 'No aplica',
            'ciudad' => 'No aplica',
        ]);
    }

    public function test_checkout_confirmation_shows_store_contact_data(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct();
        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]));

        $order = Order::first();

        $this->get(route('checkout.confirmacion', $order))
            ->assertOk()
            ->assertSee('Tiendas para contactar')
            ->assertSee('Ver detalle')
            ->assertSee('ventas-checkout@example.com')
            ->assertSee('+56 9 1111 2222')
            ->assertSee('Retiro en domicilio')
            ->assertSee('Delivery propio')
            ->assertSee('$2.500')
            ->assertSee('24 horas')
            ->assertSee('No realices transferencias')
            ->assertSee('coordinar primero la entrega con la tienda')
            ->assertSee('responsabilidad de cada tienda')
            ->assertSee('no tiene pago en linea', false);
    }

    public function test_checkout_confirmation_groups_contact_data_for_each_store(): void
    {
        $user = User::factory()->create();
        $firstProduct = $this->createPublicProduct(
            storeName: 'Tienda Uno',
            contactEmail: 'ventas.uno@example.com',
            contactPhone: '+56 9 1111 1111',
        );
        $secondProduct = $this->createPublicProduct(
            storeName: 'Tienda Dos',
            contactEmail: 'ventas.dos@example.com',
            contactPhone: '+56 9 2222 2222',
        );

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $firstProduct->id, 'qty' => 1],
                ['id' => $secondProduct->id, 'qty' => 1],
            ]));

        $order = Order::first();

        $this->get(route('checkout.confirmacion', $order))
            ->assertOk()
            ->assertSee('Tienda Uno')
            ->assertSee('ventas.uno@example.com')
            ->assertSee('+56 9 1111 1111')
            ->assertSee('Tienda Dos')
            ->assertSee('ventas.dos@example.com')
            ->assertSee('+56 9 2222 2222');
    }

    public function test_checkout_confirmation_respects_store_contact_visibility(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct(
            contactPhone: '+56 9 9999 9999',
            phoneVisible: false,
            allowWhatsapp: false,
        );

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]));

        $order = Order::first();

        $this->get(route('checkout.confirmacion', $order))
            ->assertOk()
            ->assertSee('ventas-checkout@example.com')
            ->assertDontSee('+56 9 9999 9999')
            ->assertDontSee('+56 9 3333 4444')
            ->assertSee('Sin WhatsApp público');
    }


    public function test_checkout_confirmation_requires_the_order_owner(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = $this->createPublicProduct();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]));

        $order = Order::first();

        $this->get(route('checkout.confirmacion', $order))
            ->assertOk();

        $this->actingAs($otherUser)
            ->get(route('checkout.confirmacion', $order))
            ->assertNotFound();
    }

    public function test_checkout_confirmation_requires_authentication(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct();

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]));

        auth()->logout();

        $this->get(route('checkout.confirmacion', Order::first()))
            ->assertRedirect(route('login'));
    }

    public function test_checkout_rejects_product_without_enough_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct(stock: 1);

        $this->actingAs($user)
            ->from(route('checkout.create'))
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 2],
            ]))
            ->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_checkout_rejects_product_from_inactive_store(): void
    {
        $user = User::factory()->create();
        $product = $this->createPublicProduct(storeActive: false);

        $this->actingAs($user)
            ->from(route('checkout.create'))
            ->post(route('checkout.store'), $this->checkoutPayload([
                ['id' => $product->id, 'qty' => 1],
            ]))
            ->assertRedirect(route('checkout.create'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
    }

    private function checkoutPayload(array $items): array
    {
        return [
            'cliente_telefono' => '+56912345678',
            'notas' => 'Contactar por WhatsApp',
            'cart_payload' => json_encode($items),
        ];
    }

    private function createPublicProduct(
        int $stock = 5,
        int $price = 10000,
        bool $storeActive = true,
        string $storeName = 'Tienda Checkout',
        string $contactEmail = 'ventas-checkout@example.com',
        string $contactPhone = '+56 9 1111 2222',
        bool $phoneVisible = true,
        bool $allowWhatsapp = true,
    ): Product
    {
        $category = Category::create([
            'nombre' => 'Categoria Checkout',
            'slug' => 'categoria-checkout-'.uniqid(),
            'activo' => true,
        ]);
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = Tienda::create([
            'user_id' => $vendor->id,
            'nombre' => $storeName,
            'slug' => 'tienda-checkout-'.uniqid(),
            'contacto_email' => $contactEmail,
            'contacto_telefono' => $contactPhone,
            'telefono_visible' => $phoneVisible,
            'contacto_whatsapp' => '+56 9 3333 4444',
            'permite_whatsapp' => $allowWhatsapp,
            'contacto_direccion' => 'Local Checkout 123',
            'activa' => $storeActive,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Checkout',
            'slug' => 'producto-checkout-'.uniqid(),
            'precio' => $price,
            'stock' => $stock,
            'imagen' => 'https://example.com/product.jpg',
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            'retiro_en_domicilio' => true,
            'delivery' => true,
            'envio_courier' => false,
            'costo_envio' => 2500,
            'tiempo_entrega' => '24 horas',
        ]);
    }
}
