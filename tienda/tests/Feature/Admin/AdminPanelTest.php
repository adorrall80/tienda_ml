<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductCondition;
use App\Models\SecurityBlockedTerm;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get('/admin')
            ->assertRedirect('/login');
    }

    public function test_cliente_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Productos')
            ->assertSee('Usuarios totales')
            ->assertSee('Vendedores')
            ->assertSee('Clientes');
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($admin)
            ->put('/admin/usuarios/'.$user->id, [
                'rol' => 'vendedor',
            ])
            ->assertRedirect();

        $this->assertTrue($user->fresh()->hasRole('vendedor'));
        $this->assertTrue($user->fresh()->hasRole('cliente'));
    }

    public function test_admin_cannot_update_their_own_role(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->put('/admin/usuarios/'.$admin->id, [
                'rol' => 'cliente',
            ])
            ->assertRedirect();

        $this->assertTrue($admin->fresh()->hasRole('admin'));
        $this->assertFalse($admin->fresh()->hasRole('cliente'));
    }

    public function test_admin_can_toggle_store_status(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);

        $this->actingAs($admin)
            ->patch('/admin/tiendas/'.$store->id.'/toggle')
            ->assertRedirect();

        $this->assertFalse($store->fresh()->activa);
    }

    public function test_admin_can_create_product_for_store(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $image = UploadedFile::fake()->image('producto-admin.jpg');

        $this->actingAs($admin)
            ->post('/admin/productos', [
                'nombre' => 'Producto Admin Test',
                'sku' => 'SL00998',
                'descripcion' => '<strong>Producto creado desde admin.</strong><script>alert(1)</script>',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => '39.990',
                'precio_oferta' => '29.990',
                'stock' => 11,
                'imagen_archivo' => $image,
                'envio_gratis' => '1',
                'retiro_en_domicilio' => '1',
                'delivery' => '1',
                'envio_courier' => '1',
                'costo_envio' => '3.990',
                'tiempo_entrega' => '2 a 3 días',
                'destacado' => '1',
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
                'estado_revision_id' => Product::REVISION_APROBADO,
                'atributos' => [
                    ['nombre' => 'Marca', 'valor' => 'Samsung'],
                    ['nombre' => 'Color', 'valor' => 'Negro'],
                    ['nombre' => '', 'valor' => 'Fila vacia ignorada'],
                ],
                'guardar_accion' => 'listado',
            ])
            ->assertRedirect('/admin/productos');

        $product = Product::where('nombre', 'Producto Admin Test')->firstOrFail();

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Admin Test',
            'sku' => 'SL00998',
            'slug' => 'producto-admin-test',
            'precio' => 39990,
            'precio_oferta' => 29990,
            'activo' => true,
            'retiro_en_domicilio' => true,
            'delivery' => true,
            'envio_courier' => true,
            'costo_envio' => 3990,
            'tiempo_entrega' => '2 a 3 días',
            'destacado' => true,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            'estado_revision_id' => Product::REVISION_APROBADO,
        ]);
        $this->assertStringStartsWith('/storage/products/', $product->imagen);
        $this->assertSame('<strong>Producto creado desde admin.</strong>', $product->descripcion);
        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $product->id,
            'nombre' => 'Marca',
            'valor' => 'Samsung',
            'orden' => 1,
        ]);
        $this->assertDatabaseHas('product_attributes', [
            'product_id' => $product->id,
            'nombre' => 'Color',
            'valor' => 'Negro',
            'orden' => 2,
        ]);
        $this->assertDatabaseMissing('product_attributes', [
            'product_id' => $product->id,
            'valor' => 'Fila vacia ignorada',
        ]);
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $product->imagen));
    }

    public function test_admin_products_searches_name_short_description_and_description(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();

        Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Visible Admin',
            'slug' => 'producto-visible-admin',
            'descripcion_corta' => 'Texto unico corto admin',
            'descripcion' => 'Descripcion normal.',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            'estado_id' => Product::ESTADO_NUEVO,
        ]);

        Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Completo Admin',
            'slug' => 'producto-completo-admin',
            'descripcion_corta' => 'Texto comun.',
            'descripcion' => '<p>Texto unico completo admin</p>',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            'estado_id' => Product::ESTADO_NUEVO,
        ]);

        Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Oculto Admin',
            'slug' => 'producto-oculto-admin',
            'descripcion_corta' => 'No coincide.',
            'descripcion' => 'No coincide.',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
        ]);

        $this->actingAs($admin)
            ->get('/admin/productos?q=unico')
            ->assertOk()
            ->assertSee('Producto Visible Admin')
            ->assertSee('Producto Completo Admin')
            ->assertDontSee('Producto Oculto Admin');
    }

    public function test_admin_products_can_be_paginated_with_selected_page_size(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();

        for ($i = 1; $i <= 12; $i++) {
            Product::create([
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'nombre' => 'Producto Paginado Admin '.$i,
                'slug' => 'producto-paginado-admin-'.$i,
                'precio' => 10000,
                'stock' => 5,
                'activo' => true,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            ]);
        }

        $this->actingAs($admin)
            ->get('/admin/productos?per_page=10')
            ->assertOk()
            ->assertViewHas('perPage', 10)
            ->assertViewHas('productos', fn ($productos) => $productos->perPage() === 10 && $productos->total() === 12);
    }

    public function test_admin_product_save_actions_redirect_to_new_form_or_edit_form(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();

        $this->actingAs($admin)
            ->post('/admin/productos', [
                'nombre' => 'Producto Admin Nuevo Siguiente',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 29990,
                'stock' => 11,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
                'guardar_accion' => 'nuevo',
            ])
            ->assertRedirect(route('admin.productos.create'));

        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Admin Guardar Edit',
            'slug' => 'producto-admin-guardar-edit',
            'precio' => 29990,
            'stock' => 11,
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ]);

        $this->actingAs($admin)
            ->put('/admin/productos/'.$product->id, [
                'nombre' => 'Producto Admin Guardar Editado',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 29990,
                'stock' => 11,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
                'guardar_accion' => 'guardar',
            ])
            ->assertRedirect(route('admin.productos.edit', $product));
    }

    public function test_admin_can_preview_product_from_editor(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Preview Admin',
            'slug' => 'producto-preview-admin',
            'precio' => 10000,
            'stock' => 5,
            'activo' => false,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_PAUSADO,
            'estado_revision_id' => Product::REVISION_PENDIENTE,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.productos.edit', $product))
            ->assertOk()
            ->assertSee('Vista previa');

        $this->actingAs($admin)
            ->get(route('admin.productos.preview', $product))
            ->assertOk()
            ->assertSee('Producto Preview Admin');
    }

    public function test_admin_product_image_must_be_an_image_file(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $file = UploadedFile::fake()->create('producto.txt', 8, 'text/plain');

        $this->actingAs($admin)
            ->post('/admin/productos', [
                'nombre' => 'Producto Imagen Insegura',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 29990,
                'stock' => 11,
                'imagen_archivo' => $file,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            ])
            ->assertSessionHasErrors('imagen_archivo');
    }

    public function test_admin_can_upload_and_delete_product_gallery_images(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Galeria Admin',
            'slug' => 'producto-galeria-admin',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ]);

        $this->actingAs($admin)
            ->put('/admin/productos/'.$product->id, [
                'nombre' => 'Producto Galeria Admin',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 10000,
                'stock' => 5,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
                'galeria_archivos' => [
                    UploadedFile::fake()->image('galeria-admin-1.jpg'),
                    UploadedFile::fake()->image('galeria-admin-2.webp'),
                ],
            ])
            ->assertRedirect(route('admin.productos.edit', $product));

        $this->assertCount(2, $product->fresh()->images);

        $image = $product->fresh()->images()->firstOrFail();
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $image->imagen));

        $this->actingAs($admin)
            ->delete(route('admin.productos.imagenes.destroy', [$product, $image]))
            ->assertRedirect();

        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing(str_replace('/storage/', '', $image->imagen));
    }

    public function test_admin_can_reorder_product_gallery_images(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Orden Galeria Admin',
            'slug' => 'producto-orden-galeria-admin',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ]);
        $first = $product->images()->create(['imagen' => 'https://example.com/a.jpg', 'orden' => 1]);
        $second = $product->images()->create(['imagen' => 'https://example.com/b.jpg', 'orden' => 2]);

        $this->actingAs($admin)
            ->patch(route('admin.productos.imagenes.orden', [$product, $second]), [
                'direction' => 'up',
            ])
            ->assertRedirect();

        $this->assertSame(2, $first->fresh()->orden);
        $this->assertSame(1, $second->fresh()->orden);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($admin)
            ->delete('/admin/usuarios/'.$user->id)
            ->assertRedirect();

        $this->assertNull($user->fresh());
    }

    public function test_admin_dashboard_shows_orders_summary(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $this->createOrderItemForStore($store, [
            'numero' => 'ORD-ADMIN-DASH',
            'total' => 15000,
        ], [
            'total' => 15000,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Pedidos')
            ->assertSee('1');
    }

    public function test_admin_can_view_global_orders(): void
    {
        $admin = $this->createAdmin();
        $firstVendor = User::factory()->create();
        $firstVendor->assignRole('vendedor');
        $secondVendor = User::factory()->create();
        $secondVendor->assignRole('vendedor');
        $firstStore = $this->createStore($firstVendor, [
            'nombre' => 'Tienda Admin Uno',
            'slug' => 'tienda-admin-uno',
        ]);
        $secondStore = $this->createStore($secondVendor, [
            'nombre' => 'Tienda Admin Dos',
            'slug' => 'tienda-admin-dos',
        ]);

        $this->createOrderItemForStore($firstStore, [
            'numero' => 'ORD-ADMIN-001',
            'cliente_nombre' => 'Cliente Uno',
        ], [
            'producto_nombre' => 'Producto Admin Uno',
        ]);
        $this->createOrderItemForStore($secondStore, [
            'numero' => 'ORD-ADMIN-002',
            'cliente_nombre' => 'Cliente Dos',
        ], [
            'producto_nombre' => 'Producto Admin Dos',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pedidos.index'))
            ->assertOk()
            ->assertSee('Pedidos globales')
            ->assertSee('ORD-ADMIN-001')
            ->assertSee('ORD-ADMIN-002')
            ->assertSee('Cliente Uno')
            ->assertSee('Cliente Dos');
    }

    public function test_admin_can_view_order_detail_with_all_stores(): void
    {
        $admin = $this->createAdmin();
        $firstVendor = User::factory()->create();
        $firstVendor->assignRole('vendedor');
        $secondVendor = User::factory()->create();
        $secondVendor->assignRole('vendedor');
        $firstStore = $this->createStore($firstVendor, [
            'nombre' => 'Tienda Detalle Uno',
            'slug' => 'tienda-detalle-uno',
        ]);
        $secondStore = $this->createStore($secondVendor, [
            'nombre' => 'Tienda Detalle Dos',
            'slug' => 'tienda-detalle-dos',
        ]);
        $order = $this->createOrder([
            'numero' => 'ORD-ADMIN-DETALLE',
            'cliente_nombre' => 'Cliente Detalle',
            'total' => 30000,
            'subtotal' => 30000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'tienda_id' => $firstStore->id,
            'tienda_nombre' => $firstStore->nombre,
            'producto_nombre' => 'Producto Uno Detalle',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'tienda_id' => $secondStore->id,
            'tienda_nombre' => $secondStore->nombre,
            'producto_nombre' => 'Producto Dos Detalle',
            'cantidad' => 2,
            'precio_unitario' => 10000,
            'total' => 20000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pedidos.show', $order))
            ->assertOk()
            ->assertSee('ORD-ADMIN-DETALLE')
            ->assertSee('Cliente Detalle')
            ->assertSee('Tienda Detalle Uno')
            ->assertSee('Tienda Detalle Dos')
            ->assertSee('Producto Uno Detalle')
            ->assertSee('Producto Dos Detalle');
    }

    public function test_non_admin_cannot_view_global_orders(): void
    {
        $client = User::factory()->create();
        $client->assignRole('cliente');

        $this->actingAs($client)
            ->get(route('admin.pedidos.index'))
            ->assertForbidden();
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store, [
            'numero' => 'ORD-ADMIN-ESTADO',
        ]);
        $order = $item->order;

        $this->actingAs($admin)
            ->patch(route('admin.pedidos.estado', $order), [
                'estado' => 'entregado',
            ])
            ->assertRedirect();

        $this->assertSame('entregado', $order->fresh()->estado);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'user_id' => $admin->id,
            'actor' => 'admin',
            'estado_anterior' => 'pendiente',
            'estado_nuevo' => 'entregado',
        ]);
    }

    public function test_admin_order_status_must_be_valid(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;

        $this->actingAs($admin)
            ->patch(route('admin.pedidos.estado', $order), [
                'estado' => 'estado-inventado',
            ])
            ->assertSessionHasErrors('estado');

        $this->assertSame('pendiente', $order->fresh()->estado);
    }

    public function test_admin_can_add_internal_note_to_order(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;

        $this->actingAs($admin)
            ->post(route('admin.pedidos.notas', $order), [
                'nota' => 'Llamar a vendedor para confirmar disponibilidad.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_internal_notes', [
            'order_id' => $order->id,
            'user_id' => $admin->id,
            'actor' => 'admin',
            'nota' => 'Llamar a vendedor para confirmar disponibilidad.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.pedidos.show', $order))
            ->assertOk()
            ->assertSee('Notas internas')
            ->assertSee('Llamar a vendedor para confirmar disponibilidad.');
    }

    public function test_admin_order_detail_shows_tracking_context(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $item = $this->createOrderItemForStore($store);
        $order = $item->order;
        $order->recordStatusChange($admin, 'confirmado', 'admin');

        $this->actingAs($admin)
            ->get(route('admin.pedidos.show', $order))
            ->assertOk()
            ->assertSee('Historial de estados')
            ->assertSee('Pendiente')
            ->assertSee('Confirmado')
            ->assertSee('Coordinar entrega o retiro directamente con la tienda.');
    }

    public function test_admin_can_manage_blocked_security_terms(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route('admin.seguridad.palabras.store'), [
                'term' => 'payloadtest',
            ])
            ->assertRedirect();

        $term = SecurityBlockedTerm::where('term', 'payloadtest')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.seguridad.palabras.index'))
            ->assertOk()
            ->assertSee('payloadtest');

        $this->actingAs($admin)
            ->put(route('admin.seguridad.palabras.update', $term), [
                'term' => 'payloadtest2',
                'active' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('security_blocked_terms', [
            'id' => $term->id,
            'term' => 'payloadtest2',
            'active' => true,
        ]);
    }

    public function test_custom_blocked_security_term_rejects_saved_text(): void
    {
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        SecurityBlockedTerm::create(['term' => 'payloadtest', 'active' => true]);

        $this->actingAs($admin)
            ->post('/admin/productos', [
                'nombre' => 'Producto payloadtest',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 29990,
                'stock' => 11,
                'estado_id' => Product::ESTADO_NUEVO,
                'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
            ])
            ->assertSessionHasErrors('nombre');

        $this->assertDatabaseMissing('products', [
            'nombre' => 'Producto payloadtest',
        ]);
    }

    public function test_admin_can_manage_delivery_types(): void
    {
        $admin = $this->createAdmin();
        $retiro = DeliveryType::where('slug', 'retiro-en-domicilio')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.tipos-entrega.index'))
            ->assertOk()
            ->assertSee('Tipos de entrega')
            ->assertSee($retiro->nombre);

        $this->actingAs($admin)
            ->post(route('admin.mantenedores.tipos-entrega.store'), [
                'nombre' => 'Punto de encuentro',
                'orden' => 4,
                'activo' => '1',
            ])
            ->assertRedirect();

        $deliveryType = DeliveryType::where('slug', 'punto-de-encuentro')->firstOrFail();

        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryType->id,
            'nombre' => 'Punto de encuentro',
            'orden' => 4,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.mantenedores.tipos-entrega.update', $deliveryType), [
                'nombre' => 'Punto acordado',
                'orden' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('delivery_types', [
            'id' => $deliveryType->id,
            'nombre' => 'Punto acordado',
            'slug' => 'punto-acordado',
            'orden' => 5,
            'activo' => false,
        ]);

        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        $product = Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Con Entrega',
            'slug' => 'producto-con-entrega',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_id' => Product::ESTADO_NUEVO,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ]);
        $product->deliveryTypes()->sync([$retiro->id]);

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.tipos-entrega.destroy', $retiro))
            ->assertSessionHasErrors('delivery_type');

        $this->assertDatabaseHas('delivery_types', ['id' => $retiro->id]);

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.tipos-entrega.index', ['tipo' => $retiro->id]))
            ->assertOk()
            ->assertSee('Productos asociados: '.$retiro->nombre)
            ->assertSee('Producto Con Entrega')
            ->assertSee('Editar producto');

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.tipos-entrega.destroy', $deliveryType))
            ->assertRedirect();

        $this->assertDatabaseMissing('delivery_types', ['id' => $deliveryType->id]);
    }

    public function test_admin_can_manage_product_conditions(): void
    {
        $admin = $this->createAdmin();
        $nuevo = ProductCondition::where('slug', 'nuevo')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.estados-producto.index'))
            ->assertOk()
            ->assertSee('Estados de producto')
            ->assertSee($nuevo->nombre);

        $this->actingAs($admin)
            ->post(route('admin.mantenedores.estados-producto.store'), [
                'nombre' => 'Semi nuevo',
                'orden' => 4,
                'activo' => '1',
            ])
            ->assertRedirect();

        $condition = ProductCondition::where('slug', 'semi-nuevo')->firstOrFail();

        $this->assertDatabaseHas('product_conditions', [
            'id' => $condition->id,
            'nombre' => 'Semi nuevo',
            'orden' => 4,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.mantenedores.estados-producto.update', $condition), [
                'nombre' => 'Casi nuevo',
                'orden' => 5,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('product_conditions', [
            'id' => $condition->id,
            'nombre' => 'Casi nuevo',
            'slug' => 'casi-nuevo',
            'orden' => 5,
            'activo' => false,
        ]);

        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();
        Product::create([
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Con Estado',
            'slug' => 'producto-con-estado',
            'precio' => 10000,
            'stock' => 5,
            'activo' => true,
            'estado_id' => $nuevo->id,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.estados-producto.destroy', $nuevo))
            ->assertSessionHasErrors('condition');

        $this->assertDatabaseHas('product_conditions', ['id' => $nuevo->id]);

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.estados-producto.index', ['estado' => $nuevo->id]))
            ->assertOk()
            ->assertSee('Productos asociados: '.$nuevo->nombre)
            ->assertSee('Producto Con Estado')
            ->assertSee('Editar producto');

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.estados-producto.destroy', $condition))
            ->assertRedirect();

        $this->assertDatabaseMissing('product_conditions', ['id' => $condition->id]);
    }

    public function test_admin_can_manage_order_statuses(): void
    {
        $admin = $this->createAdmin();
        $pending = OrderStatus::where('slug', 'pendiente')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.estados-pedido.index'))
            ->assertOk()
            ->assertSee('Estados de pedido')
            ->assertSee($pending->nombre);

        $this->actingAs($admin)
            ->post(route('admin.mantenedores.estados-pedido.store'), [
                'nombre' => 'Listo para retiro',
                'orden' => 6,
                'activo' => '1',
            ])
            ->assertRedirect();

        $status = OrderStatus::where('slug', 'listo-para-retiro')->firstOrFail();

        $this->assertDatabaseHas('order_statuses', [
            'id' => $status->id,
            'nombre' => 'Listo para retiro',
            'orden' => 6,
            'activo' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.mantenedores.estados-pedido.update', $status), [
                'nombre' => 'Retiro listo',
                'orden' => 7,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('order_statuses', [
            'id' => $status->id,
            'nombre' => 'Retiro listo',
            'slug' => 'retiro-listo',
            'orden' => 7,
            'activo' => false,
        ]);

        $order = $this->createOrder([
            'numero' => 'ORD-STATUS-001',
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.estados-pedido.destroy', $pending))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('order_statuses', ['id' => $pending->id]);

        $this->actingAs($admin)
            ->get(route('admin.mantenedores.estados-pedido.index', ['estado' => $pending->slug]))
            ->assertOk()
            ->assertSee('Pedidos asociados: '.$pending->nombre)
            ->assertSee($order->numero)
            ->assertSee('Ver pedido');

        $this->actingAs($admin)
            ->delete(route('admin.mantenedores.estados-pedido.destroy', $status))
            ->assertRedirect();

        $this->assertDatabaseMissing('order_statuses', ['id' => $status->id]);
    }

    private function createAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'nombre' => 'Categoria Admin Test',
            'slug' => 'categoria-admin-test',
            'orden' => 1,
            'activo' => true,
            'estado_publicacion_id' => Product::PUBLICACION_ACTIVO,
        ], $overrides));
    }

    private function createStore(User $user, array $overrides = []): Tienda
    {
        return Tienda::create(array_merge([
            'user_id' => $user->id,
            'nombre' => 'Tienda Admin Test',
            'slug' => 'tienda-admin-test',
            'descripcion' => 'Descripcion test.',
            'activa' => true,
        ], $overrides));
    }

    private function createOrder(array $overrides = []): Order
    {
        return Order::create(array_merge([
            'numero' => 'ORD-ADMIN-'.uniqid(),
            'cliente_nombre' => 'Cliente Admin Test',
            'cliente_email' => 'cliente-admin@example.com',
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
            'tienda_nombre' => $store->nombre,
            'producto_nombre' => 'Producto Pedido Admin',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ], $itemOverrides));
    }
}
