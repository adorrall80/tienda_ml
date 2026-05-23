<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/mi-cuenta');

        $response->assertOk();
    }

    public function test_profile_page_shows_registered_user_orders(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'numero' => 'ORD-CUENTA-001',
            'user_id' => $user->id,
            'cliente_nombre' => $user->name,
            'cliente_email' => $user->email,
            'direccion' => 'Calle Cuenta 123',
            'comuna' => 'Santiago',
            'ciudad' => 'Santiago',
            'subtotal' => 10000,
            'total' => 10000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'producto_nombre' => 'Producto Cuenta',
            'tienda_nombre' => 'Tienda Cuenta',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ]);

        $this->actingAs($user)
            ->get('/mi-cuenta')
            ->assertOk()
            ->assertSee('ORD-CUENTA-001')
            ->assertSee('Producto Cuenta')
            ->assertSee('Tienda Cuenta')
            ->assertDontSee('Aún no tienes compras');
    }

    public function test_cliente_can_activate_seller_role_from_account(): void
    {
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($user)
            ->post(route('cuenta.vendedor.activar'))
            ->assertRedirect(route('vendedor.tienda.create'));

        $user->refresh();

        $this->assertTrue($user->hasRole('cliente'));
        $this->assertTrue($user->hasRole('vendedor'));
    }

    public function test_user_can_view_only_their_order_detail_from_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::create([
            'numero' => 'ORD-CUENTA-002',
            'user_id' => $user->id,
            'cliente_nombre' => $user->name,
            'cliente_email' => $user->email,
            'direccion' => 'No aplica',
            'comuna' => 'No aplica',
            'ciudad' => 'No aplica',
            'subtotal' => 10000,
            'total' => 10000,
        ]);
        $otherOrder = Order::create([
            'numero' => 'ORD-CUENTA-003',
            'user_id' => $otherUser->id,
            'cliente_nombre' => $otherUser->name,
            'cliente_email' => $otherUser->email,
            'direccion' => 'No aplica',
            'comuna' => 'No aplica',
            'ciudad' => 'No aplica',
            'subtotal' => 5000,
            'total' => 5000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'producto_nombre' => 'Producto Detalle',
            'tienda_nombre' => 'Tienda Detalle',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ]);

        $this->actingAs($user)
            ->get(route('cuenta.compras.show', $order))
            ->assertOk()
            ->assertSee('ORD-CUENTA-002')
            ->assertSee('Producto Detalle')
            ->assertSee('Tienda Detalle');

        $this->actingAs($user)
            ->get(route('cuenta.compras.show', $otherOrder))
            ->assertNotFound();
    }

    public function test_user_order_detail_shows_tracking_summary(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create();
        $order = Order::create([
            'numero' => 'ORD-CUENTA-SEGUIMIENTO',
            'user_id' => $user->id,
            'cliente_nombre' => $user->name,
            'cliente_email' => $user->email,
            'direccion' => 'No aplica',
            'comuna' => 'No aplica',
            'ciudad' => 'No aplica',
            'subtotal' => 10000,
            'total' => 10000,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'producto_nombre' => 'Producto Seguimiento',
            'tienda_nombre' => 'Tienda Seguimiento',
            'cantidad' => 1,
            'precio_unitario' => 10000,
            'total' => 10000,
        ]);
        $order->recordStatusChange($admin, 'confirmado', 'admin');

        $this->actingAs($user)
            ->get(route('cuenta.compras.show', $order))
            ->assertOk()
            ->assertSee('Historial de seguimiento')
            ->assertSee('Confirmado')
            ->assertSee('Actualizado por administracion')
            ->assertSee('Coordinar entrega o retiro directamente con la tienda.');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/mi-cuenta', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/mi-cuenta');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/mi-cuenta', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/mi-cuenta');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/mi-cuenta', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/mi-cuenta')
            ->delete('/mi-cuenta', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/mi-cuenta');

        $this->assertNotNull($user->fresh());
    }
}
