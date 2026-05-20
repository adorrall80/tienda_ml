<?php

namespace Tests\Feature\Vendedor;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendedorPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendedor_dashboard_requires_authentication(): void
    {
        $this->get('/mi-tienda')
            ->assertRedirect('/login');
    }

    public function test_cliente_cannot_access_vendedor_dashboard(): void
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
        $user = User::factory()->create();
        $user->assignRole('vendedor');
        $store = $this->createStore($user);
        $category = $this->createCategory();

        $this->actingAs($user)
            ->post('/mi-tienda/productos', [
                'nombre' => 'Producto Vendedor Test',
                'descripcion' => 'Producto creado desde panel vendedor.',
                'category_id' => $category->id,
                'precio' => 15990,
                'precio_original' => 19990,
                'stock' => 7,
                'imagen' => 'https://example.com/producto-vendedor.jpg',
                'envio_gratis' => '1',
                'cuotas' => 3,
                'estado' => 'nuevo',
            ])
            ->assertRedirect('/mi-tienda/productos');

        $this->assertDatabaseHas('products', [
            'tienda_id' => $store->id,
            'category_id' => $category->id,
            'nombre' => 'Producto Vendedor Test',
            'slug' => 'producto-vendedor-test',
            'activo' => true,
        ]);
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
}
