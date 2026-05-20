<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->assertFalse($user->fresh()->hasRole('cliente'));
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
        $admin = $this->createAdmin();
        $vendor = User::factory()->create();
        $vendor->assignRole('vendedor');
        $store = $this->createStore($vendor);
        $category = $this->createCategory();

        $this->actingAs($admin)
            ->post('/admin/productos', [
                'nombre' => 'Producto Admin Test',
                'descripcion' => 'Producto creado desde admin.',
                'category_id' => $category->id,
                'tienda_id' => $store->id,
                'precio' => 29990,
                'precio_original' => 39990,
                'stock' => 11,
                'imagen' => 'https://example.com/producto-admin.jpg',
                'envio_gratis' => '1',
                'cuotas' => 6,
                'activo' => '1',
                'estado' => 'nuevo',
            ])
            ->assertRedirect('/admin/productos');

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'tienda_id' => $store->id,
            'nombre' => 'Producto Admin Test',
            'slug' => 'producto-admin-test',
            'activo' => true,
        ]);
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
}
