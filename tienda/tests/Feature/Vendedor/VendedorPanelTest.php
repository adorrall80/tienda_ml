<?php

namespace Tests\Feature\Vendedor;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VendedorPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendedor_panel_requires_authentication(): void
    {
        $this->get('/mi-tienda')
            ->assertRedirect('/login');
    }

    public function test_cliente_cannot_access_vendedor_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($user)
            ->get('/mi-tienda')
            ->assertForbidden();
    }

    public function test_vendedor_without_store_sees_create_store_prompt(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');

        $this->actingAs($user)
            ->get('/mi-tienda')
            ->assertOk()
            ->assertSee('no tienes una tienda')
            ->assertSee('Crear mi tienda');
    }

    public function test_vendedor_can_create_store(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');

        $this->actingAs($user)
            ->post('/mi-tienda/crear-tienda', [
                'nombre' => 'Tienda Test Vendedor',
                'descripcion' => 'Descripcion de tienda test.',
            ])
            ->assertRedirect('/mi-tienda');

        $this->assertDatabaseHas('tiendas', [
            'user_id' => $user->id,
            'nombre' => 'Tienda Test Vendedor',
            'slug' => 'tienda-test-vendedor',
            'activa' => true,
        ]);
    }

    public function test_vendedor_can_create_product_for_their_store(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->assignRole('vendedor');
        $store = $this->createStore($user);
        $category = $this->createCategory();
        $image = UploadedFile::fake()->image('producto-vendedor.png');

        $this->actingAs($user)
            ->post('/mi-tienda/productos', [
                'nombre' => 'Producto Vendedor Test',
                'sku' => '000001',
                'descripcion' => 'Producto creado desde panel vendedor.',
                'category_id' => $category->id,
                'precio' => 19990,
                'precio_oferta' => 15990,
                'stock' => 7,
                'imagen_archivo' => $image,
                'envio_gratis' => '1',
                'estado' => 'nuevo',
            ])
            ->assertRedirect('/mi-tienda/productos');

        $product = Product::where('nombre', 'Producto Vendedor Test')->firstOrFail();

        $this->assertDatabaseHas('products', [
            'tienda_id' => $store->id,
            'category_id' => $category->id,
            'nombre' => 'Producto Vendedor Test',
            'sku' => '000001',
            'slug' => 'producto-vendedor-test',
            'activo' => true,
        ]);
        $this->assertStringStartsWith('/storage/products/', $product->imagen);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->imagen));
    }

    public function test_vendedor_product_image_must_be_an_image_file(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');
        $this->createStore($user);
        $category = $this->createCategory();
        $file = UploadedFile::fake()->create('producto.txt', 8, 'text/plain');

        $this->actingAs($user)
            ->post('/mi-tienda/productos', [
                'nombre' => 'Producto Imagen Insegura',
                'category_id' => $category->id,
                'precio' => 15990,
                'stock' => 7,
                'imagen_archivo' => $file,
                'estado' => 'nuevo',
            ])
            ->assertSessionHasErrors('imagen_archivo');
    }

    public function test_vendedor_cannot_edit_product_from_another_store(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('vendedor');
        $intruder = User::factory()->create();
        $intruder->assignRole('vendedor');

        $ownerStore = $this->createStore($owner, [
            'nombre' => 'Tienda Propietaria',
            'slug' => 'tienda-propietaria',
        ]);
        $this->createStore($intruder, [
            'nombre' => 'Tienda Intrusa',
            'slug' => 'tienda-intrusa',
        ]);
        $category = $this->createCategory();
        $product = $this->createProduct($category, $ownerStore);

        $this->actingAs($intruder)
            ->get('/mi-tienda/productos/'.$product->id.'/editar')
            ->assertForbidden();
    }

    public function test_vendedor_panel_shows_received_orders_for_their_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $this->createOrderItemForStore($store, [
            'numero' => 'ORD-VENDEDOR-001',
            'cliente_nombre' => 'Cliente Pedido',
        ], [
            'producto_nombre' => 'Producto Recibido',
            'cantidad' => 2,
            'total' => 20000,
        ]);

        $this->actingAs($vendor)
            ->get('/mi-tienda')
            ->assertOk()
            ->assertSee('Pedidos recibidos')
            ->assertSee('ORD-VENDEDOR-001')
            ->assertSee('Cliente Pedido')
            ->assertSee('$20.000');
    }

    public function test_vendedor_orders_index_shows_only_orders_for_their_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $otherStore = $this->createStore($otherVendor, [
            'nombre' => 'Tienda Otra',
            'slug' => 'tienda-otra',
        ]);

        $this->createOrderItemForStore($store, ['numero' => 'ORD-MIA-001']);
        $this->createOrderItemForStore($otherStore, ['numero' => 'ORD-AJENA-001']);

        $this->actingAs($vendor)
            ->get(route('vendedor.pedidos.index'))
            ->assertOk()
            ->assertSee('ORD-MIA-001')
            ->assertDontSee('ORD-AJENA-001');
    }

    public function test_vendedor_order_detail_only_shows_items_from_their_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $otherStore = $this->createStore($otherVendor, [
            'nombre' => 'Tienda Otra',
            'slug' => 'tienda-otra',
        ]);
        $order = $this->createOrder([
            'numero' => 'ORD-MIXTA-001',
            'cliente_nombre' => 'Cliente Mixto',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'tienda_id' => $store->id,
            'producto_nombre' => 'Producto De Mi Tienda',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'tienda_id' => $otherStore->id,
            'producto_nombre' => 'Producto Ajeno En Misma Orden',
            'cantidad' => 1,
            'precio_unitario' => 5000,
            'total' => 5000,
        ]);
        $otherOrder = $this->createOrder(['numero' => 'ORD-SOLO-AJENA']);
        OrderItem::create([
            'order_id' => $otherOrder->id,
            'tienda_id' => $otherStore->id,
            'producto_nombre' => 'Producto Solo Ajeno',
            'cantidad' => 1,
            'precio_unitario' => 5000,
            'total' => 5000,
        ]);

        $this->actingAs($vendor)
            ->get(route('vendedor.pedidos.show', $order))
            ->assertOk()
            ->assertSee('ORD-MIXTA-001')
            ->assertSee('Cliente Mixto')
            ->assertSee('Producto De Mi Tienda')
            ->assertDontSee('Producto Ajeno En Misma Orden');

        $this->actingAs($vendor)
            ->get(route('vendedor.pedidos.show', $otherOrder))
            ->assertNotFound();
    }

    public function test_vendedor_can_update_status_for_order_from_their_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store, [
            'numero' => 'ORD-VENDEDOR-ESTADO',
        ]);
        $order = $item->order;

        $this->actingAs($vendor)
            ->patch(route('vendedor.pedidos.estado', $order), [
                'estado' => 'entregado',
            ])
            ->assertRedirect();

        $this->assertSame('entregado', $order->fresh()->estado);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'user_id' => $vendor->id,
            'actor' => 'vendedor',
            'estado_anterior' => 'pendiente',
            'estado_nuevo' => 'entregado',
        ]);
    }

    public function test_vendedor_cannot_update_status_for_order_from_another_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendedor');
        $this->createStore($vendor);
        $otherStore = $this->createStore($otherVendor, [
            'nombre' => 'Tienda Estado Ajena',
            'slug' => 'tienda-estado-ajena',
        ]);
        $item = $this->createOrderItemForStore($otherStore, [
            'numero' => 'ORD-VENDEDOR-AJENA-ESTADO',
        ]);
        $order = $item->order;

        $this->actingAs($vendor)
            ->patch(route('vendedor.pedidos.estado', $order), [
                'estado' => 'confirmado',
            ])
            ->assertNotFound();

        $this->assertSame('pendiente', $order->fresh()->estado);
    }

    public function test_vendedor_can_add_internal_note_to_order_from_their_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;

        $this->actingAs($vendor)
            ->post(route('vendedor.pedidos.notas', $order), [
                'nota' => 'Cliente pidio coordinar por WhatsApp.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_internal_notes', [
            'order_id' => $order->id,
            'user_id' => $vendor->id,
            'tienda_id' => $store->id,
            'actor' => 'vendedor',
            'nota' => 'Cliente pidio coordinar por WhatsApp.',
        ]);

        $this->actingAs($vendor)
            ->get(route('vendedor.pedidos.show', $order))
            ->assertOk()
            ->assertSee('Notas internas')
            ->assertSee('Cliente pidio coordinar por WhatsApp.');
    }

    public function test_vendedor_cannot_add_internal_note_to_order_from_another_store(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $otherVendor = User::factory()->create();
        $otherVendor->assignRole('vendedor');
        $this->createStore($vendor);
        $otherStore = $this->createStore($otherVendor, [
            'nombre' => 'Tienda Nota Ajena',
            'slug' => 'tienda-nota-ajena',
        ]);
        $item = $this->createOrderItemForStore($otherStore);
        $order = $item->order;

        $this->actingAs($vendor)
            ->post(route('vendedor.pedidos.notas', $order), [
                'nota' => 'Nota no autorizada',
            ])
            ->assertNotFound();

        $this->assertDatabaseMissing('order_internal_notes', [
            'order_id' => $order->id,
            'nota' => 'Nota no autorizada',
        ]);
    }

    public function test_vendedor_order_detail_shows_tracking_context(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;
        $order->recordStatusChange($vendor, 'confirmado', 'vendedor');

        $this->actingAs($vendor)
            ->get(route('vendedor.pedidos.show', $order))
            ->assertOk()
            ->assertSee('Historial de estados')
            ->assertSee('Pendiente')
            ->assertSee('Confirmado')
            ->assertSee('Coordinar entrega o retiro directamente con la tienda.');
    }

    public function test_vendedor_order_status_must_be_valid(): void
    {
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;

        $this->actingAs($vendor)
            ->patch(route('vendedor.pedidos.estado', $order), [
                'estado' => 'estado-inventado',
            ])
            ->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $order->fresh()->estado);
    }

    private function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'nombre' => 'Categoria Vendedor Test',
            'slug' => 'categoria-vendedor-test',
            'orden' => 1,
            'activo' => true,
        ], $overrides));
    }

    private function createStore(User $user, array $overrides = []): Tienda
    {
        return Tienda::create(array_merge([
            'user_id' => $user->id,
            'nombre' => 'Tienda Vendedor Test',
            'slug' => 'tienda-vendedor-test',
            'descripcion' => 'Descripcion test.',
            'activa' => true,
        ], $overrides));
    }

    private function createProduct(Category $category, Tienda $store, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Ajeno Test',
            'slug' => 'producto-ajeno-test',
            'descripcion' => 'Descripcion test.',
            'precio' => 10000,
            'stock' => 3,
            'imagen' => 'https://example.com/producto-ajeno.jpg',
            'envio_gratis' => false,
            'activo' => true,
            'estado' => 'nuevo',
        ], $overrides));
    }

    private function createOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'numero' => 'ORD-VENDEDOR-'.uniqid(),
            'cliente_nombre' => 'Cliente Test',
            'cliente_email' => 'cliente-vendedor@example.com',
            'cliente_telefono' => '+56 9 1234 5678',
            'direccion' => 'No aplica',
            'comuna' => 'No aplica',
            'ciudad' => 'No aplica',
            'subtotal' => 10000,
            'total' => 10000,
        ], $overrides));
    }

    private function createOrderItemForStore(Tienda $store, array $orderOverrides = [], array $itemOverrides = []): OrderItem
    {
        $order = $this->createOrder($orderOverrides);

        return OrderItem::create(array_merge([
            'order_id' => $order->id,
            'tienda_id' => $store->id,
            'producto_nombre' => 'Producto Pedido Vendedor',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ], $itemOverrides));
    }
}
