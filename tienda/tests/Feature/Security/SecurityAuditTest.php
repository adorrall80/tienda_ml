<?php

namespace Tests\Feature\Security;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_does_not_redirect_to_an_external_intended_url(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Audit-password-123!')]);
        $this->get('/login/intended?to='.urlencode('https://example.invalid/audit'));

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Audit-password-123!',
        ]);

        $this->assertNotSame('https://example.invalid/audit', $response->headers->get('Location'));
    }

    public function test_stock_endpoint_does_not_disclose_private_product_metadata(): void
    {
        $owner = User::factory()->create();
        $store = Tienda::create([
            'user_id' => $owner->id, 'nombre' => 'Audit store',
            'slug' => 'audit-store', 'activa' => true,
        ]);
        $category = Category::create([
            'nombre' => 'Audit category', 'slug' => 'audit-category', 'activo' => true,
        ]);
        $product = Product::withoutEvents(fn () => Product::create([
            'category_id' => $category->id, 'tienda_id' => $store->id,
            'nombre' => 'CONFIDENTIAL-AUDIT-PRODUCT', 'slug' => 'private-audit-product',
            'precio' => 12345, 'stock' => 5, 'activo' => false,
            'estado_revision_id' => Product::REVISION_PENDIENTE,
            'estado_publicacion_id' => Product::PUBLICACION_PAUSADO,
        ]));

        $this->get('/productos/'.$product->slug)->assertNotFound();
        $response = $this->getJson('/carrito/validar-stock?ids='.$product->id)->assertOk();
        $response->assertDontSee('CONFIDENTIAL-AUDIT-PRODUCT');
    }

    public function test_login_throttles_repeated_failed_credentials(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/login', [
                'email' => 'audit-throttle@example.invalid', 'password' => 'invalid',
            ])->assertUnprocessable();
        }

        $response = $this->postJson('/login', [
            'email' => 'audit-throttle@example.invalid', 'password' => 'invalid',
        ])->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
        $this->assertNotSame(trans('auth.failed'), $response->json('errors.email.0'));
        $this->assertGreaterThan(0, \Illuminate\Support\Facades\RateLimiter::availableIn(
            'audit-throttle@example.invalid|127.0.0.1'
        ));
    }
}
