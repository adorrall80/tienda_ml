<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_and_non_admins_cannot_manage_categories(): void
    {
        $category = Category::create(['nombre' => 'Original', 'slug' => 'original']);
        $this->get('/admin/categorias')->assertRedirect('/login');
        foreach (['cliente', 'vendedor'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->actingAs($user)->get('/admin/categorias')->assertForbidden();
            $this->post('/admin/categorias', $this->payload())->assertForbidden();
            $this->put('/admin/categorias/'.$category->id, $this->payload())->assertForbidden();
        }
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_admin_can_create_categories_and_view_the_forms(): void
    {
        $this->loginAdmin();
        $this->get('/admin/categorias')->assertOk()->assertSee('Nueva categoría');
        $this->get('/admin/categorias/create')->assertOk();
        $this->post('/admin/categorias', $this->payload())->assertRedirect('/admin/categorias');
        $category = Category::sole();
        $this->assertSame('herramientas', $category->slug);
        $this->get('/admin/categorias/'.$category->id.'/edit')->assertOk()->assertSee('Herramientas');
        $this->get('/')->assertOk()->assertSee('Herramientas');
    }

    public function test_name_collision_generates_an_available_slug_but_explicit_duplicates_are_rejected(): void
    {
        $this->loginAdmin();
        Category::create(['nombre' => 'Herramientas', 'slug' => 'herramientas']);
        $this->post('/admin/categorias', $this->payload())->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', ['slug' => 'herramientas-2']);
        $this->post('/admin/categorias', $this->payload(['slug' => 'herramientas']))->assertSessionHasErrors('slug');
        $this->post('/admin/categorias', $this->payload(['slug' => '../bad']))->assertSessionHasErrors('slug');
    }

    public function test_admin_can_rename_reorder_and_deactivate_without_changing_existing_url(): void
    {
        $this->loginAdmin();
        $category = Category::create(['nombre' => 'Herramientas', 'slug' => 'herramientas', 'activo' => true]);
        $this->put('/admin/categorias/'.$category->id, $this->payload([
            'nombre' => 'Ferretería', 'orden' => 255, 'activo' => 0,
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id, 'nombre' => 'Ferretería', 'slug' => 'herramientas', 'orden' => 255, 'activo' => false,
        ]);
        $this->get('/')->assertDontSee('Ferretería');
        $this->put('/admin/categorias/'.$category->id, $this->payload(['orden' => 256]))->assertSessionHasErrors('orden');
    }

    public function test_subcategories_work_and_parent_cycles_are_rejected(): void
    {
        $this->loginAdmin();
        $root = Category::create(['nombre' => 'Root', 'slug' => 'root']);
        $this->post('/admin/categorias', $this->payload(['parent_id' => $root->id]))->assertSessionHasNoErrors();
        $child = Category::where('slug', 'herramientas')->sole();
        $grandchild = Category::create(['nombre' => 'Nieto', 'slug' => 'nieto', 'parent_id' => $child->id]);
        foreach ([$root->id, $child->id, $grandchild->id] as $parentId) {
            $this->put('/admin/categorias/'.$root->id, $this->payload(['parent_id' => $parentId]))
                ->assertSessionHasErrors('parent_id');
        }
        $this->assertNull($root->fresh()->parent_id);
        $this->post('/admin/categorias', $this->payload(['parent_id' => 99999]))->assertSessionHasErrors('parent_id');
    }

    public function test_category_names_are_escaped_in_admin_views(): void
    {
        $this->loginAdmin();
        Category::create(['nombre' => '<script>alert(1)</script>', 'slug' => 'xss-check']);
        $this->get('/admin/categorias')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    private function loginAdmin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['nombre' => 'Herramientas', 'orden' => 1, 'activo' => 1, 'icono' => '🔧'], $overrides);
    }
}
