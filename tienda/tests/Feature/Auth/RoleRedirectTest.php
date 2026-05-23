<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/redirect')
            ->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_vendedor_is_redirected_to_vendedor_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');

        $this->actingAs($user)
            ->get('/redirect')
            ->assertRedirect(route('vendedor.panel', absolute: false));
    }

    public function test_cliente_is_redirected_to_public_home(): void
    {
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($user)
            ->get('/redirect')
            ->assertRedirect(route('inicio', absolute: false));
    }
}
