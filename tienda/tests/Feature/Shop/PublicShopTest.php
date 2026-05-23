<?php

namespace Tests\Feature\Shop;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_can_be_rendered_with_featured_modules(): void
    {
        $category = $this->createCategory();
        $tag = Tag::create([
            'nombre' => 'Oferta del dia',
            'slug' => 'oferta',
            'label' => 'Oferta',
            'color' => 'orange',
        ]);
        $product = $this->createProduct($category, [
            'nombre' => 'Notebook Oferta Test',
            'slug' => 'notebook-oferta-test',
        ]);
        $product->tags()->attach($tag);

        Banner::create([
            'badge' => 'Oferta',
            'titulo' => 'Banner Test',
            'subtitulo' => 'Subtitulo test',
            'precio' => '$10.000',
            'imagen' => 'https://example.com/banner.jpg',
            'url' => '#',
            'btn_texto' => 'Ver oferta',
            'orden' => 1,
            'activo' => true,
        ]);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Banner Test')
            ->assertSee($category->nombre)
            ->assertSee('Notebook Oferta Test');
    }

    public function test_public_pages_include_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    public function test_products_listing_shows_active_products(): void
    {
        $category = $this->createCategory();
        $active = $this->createProduct($category, [
            'nombre' => 'Producto Activo Test',
            'slug' => 'producto-activo-test',
            'activo' => true,
        ]);
        $this->createProduct($category, [
            'nombre' => 'Producto Inactivo Test',
            'slug' => 'producto-inactivo-test',
            'activo' => false,
        ]);

        $response = $this->get('/productos');

        $response
            ->assertOk()
            ->assertSee($active->nombre)
            ->assertDontSee('Producto Inactivo Test');
    }

    public function test_products_listing_hides_products_from_inactive_stores(): void
    {
        $category = $this->createCategory();
        $activeStoreProduct = $this->createProduct($category, [
            'nombre' => 'Producto Tienda Activa Test',
            'slug' => 'producto-tienda-activa-test',
        ]);
        $inactiveStore = $this->createStore([
            'nombre' => 'Tienda Inactiva Test',
            'slug' => 'tienda-inactiva-test',
            'activa' => false,
        ]);
        $this->createProduct($category, [
            'tienda_id' => $inactiveStore->id,
            'nombre' => 'Producto Tienda Inactiva Test',
            'slug' => 'producto-tienda-inactiva-test',
        ]);

        $response = $this->get('/productos');

        $response
            ->assertOk()
            ->assertSee($activeStoreProduct->nombre)
            ->assertDontSee('Producto Tienda Inactiva Test');
    }

    public function test_products_listing_can_filter_by_category_and_search(): void
    {
        $electronics = $this->createCategory([
            'nombre' => 'Electronica Test',
            'slug' => 'electronica-test',
        ]);
        $home = $this->createCategory([
            'nombre' => 'Hogar Test',
            'slug' => 'hogar-test',
        ]);
        $matching = $this->createProduct($electronics, [
            'nombre' => 'Camara Pro Test',
            'slug' => 'camara-pro-test',
        ]);
        $this->createProduct($home, [
            'nombre' => 'Silla Hogar Test',
            'slug' => 'silla-hogar-test',
        ]);

        $response = $this->get('/productos?cat=electronica-test&q=Camara');

        $response
            ->assertOk()
            ->assertSee($matching->nombre)
            ->assertDontSee('Silla Hogar Test');
    }

    public function test_products_listing_searches_name_short_description_and_description(): void
    {
        $category = $this->createCategory();
        $byShortDescription = $this->createProduct($category, [
            'nombre' => 'Producto Uno Test',
            'slug' => 'producto-uno-test',
            'descripcion_corta' => 'Texto unico corto marketplace',
            'descripcion' => 'Descripcion comun.',
        ]);
        $byDescription = $this->createProduct($category, [
            'nombre' => 'Producto Dos Test',
            'slug' => 'producto-dos-test',
            'descripcion_corta' => 'Texto comun.',
            'descripcion' => '<strong>Texto unico completo marketplace</strong>',
        ]);
        $this->createProduct($category, [
            'nombre' => 'Producto Tres Test',
            'slug' => 'producto-tres-test',
            'descripcion_corta' => 'No coincide.',
            'descripcion' => 'No coincide.',
        ]);

        $this->get('/productos?q=unico+corto')
            ->assertOk()
            ->assertSee($byShortDescription->nombre)
            ->assertDontSee('Producto Tres Test');

        $this->get('/productos?q=unico+completo')
            ->assertOk()
            ->assertSee($byDescription->nombre)
            ->assertDontSee('Producto Tres Test');
    }

    public function test_products_listing_can_filter_by_product_status(): void
    {
        $category = $this->createCategory();
        $newProduct = $this->createProduct($category, [
            'nombre' => 'Producto Nuevo Test',
            'slug' => 'producto-nuevo-test',
            'estado' => 'nuevo',
        ]);
        $this->createProduct($category, [
            'nombre' => 'Producto Usado Test',
            'slug' => 'producto-usado-test',
            'estado' => 'usado',
        ]);

        $this->get('/productos?estado=nuevo')
            ->assertOk()
            ->assertSee($newProduct->nombre)
            ->assertSee('Estado: Nuevo')
            ->assertDontSee('Producto Usado Test');
    }

    public function test_product_detail_page_can_be_rendered(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct($category, [
            'nombre' => 'Producto Detalle Test',
            'slug' => 'producto-detalle-test',
            'descripcion' => 'Descripcion visible del producto.',
        ]);

        $response = $this->get('/productos/'.$product->slug);

        $response
            ->assertOk()
            ->assertSee($product->nombre)
            ->assertSee('Descripcion visible del producto.')
            ->assertSee('Agregar al carrito');
    }

    public function test_product_detail_shows_real_store_information(): void
    {
        $category = $this->createCategory();
        $store = $this->createStore([
            'nombre' => 'Boutique Real Test',
            'slug' => 'boutique-real-test',
            'descripcion' => 'Tienda real para pruebas.',
        ]);
        $product = $this->createProduct($category, [
            'tienda_id' => $store->id,
            'nombre' => 'Producto Con Tienda Real',
            'slug' => 'producto-con-tienda-real',
        ]);

        $this->get('/productos/'.$product->slug)
            ->assertOk()
            ->assertSee('Boutique Real Test')
            ->assertSee('Tienda real para pruebas.')
            ->assertDontSee('TiendaMV Oficial');
    }

    public function test_product_detail_returns_not_found_for_inactive_store(): void
    {
        $category = $this->createCategory();
        $inactiveStore = $this->createStore([
            'nombre' => 'Tienda Detalle Inactiva',
            'slug' => 'tienda-detalle-inactiva',
            'activa' => false,
        ]);
        $product = $this->createProduct($category, [
            'tienda_id' => $inactiveStore->id,
            'nombre' => 'Producto Oculto Detalle',
            'slug' => 'producto-oculto-detalle',
        ]);

        $this->get('/productos/'.$product->slug)
            ->assertNotFound();
    }

    public function test_cart_page_can_be_rendered(): void
    {
        $this->get('/carrito')
            ->assertOk()
            ->assertSee('Mi carrito')
            ->assertSee('Resumen de compra');
    }

    public function test_sell_link_sends_guests_to_registration(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="'.route('register').'"', false)
            ->assertDontSee('href="#" class="header-link">Quiero vender', false)
            ->assertDontSee('href="#" class="cta-btn"', false);
    }

    public function test_sell_link_sends_vendedores_to_their_store_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('href="'.route('vendedor.panel').'"', false);
    }

    public function test_sell_link_sends_clientes_to_account_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('cliente');

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee('href="'.route('cuenta.perfil').'"', false);
    }

    public function test_search_suggestions_return_matching_products_and_categories(): void
    {
        $category = $this->createCategory([
            'nombre' => 'Productos Pro',
            'slug' => 'productos-pro',
        ]);
        $product = $this->createProduct($category, [
            'nombre' => 'Camara Profesional Test',
            'slug' => 'camara-profesional-test',
            'rating_count' => 25,
        ]);

        $this->getJson('/buscar/sugerencias?q=Pro')
            ->assertOk()
            ->assertJsonFragment([$product->nombre])
            ->assertJsonFragment([$category->nombre]);
    }

    public function test_search_suggestions_search_product_descriptions(): void
    {
        $category = $this->createCategory();
        $product = $this->createProduct($category, [
            'nombre' => 'Producto Sugerencia Test',
            'slug' => 'producto-sugerencia-test',
            'descripcion_corta' => 'Texto sugerencia corto',
            'descripcion' => '<strong>Texto sugerencia completo</strong>',
        ]);

        $this->getJson('/buscar/sugerencias?q=sugerencia+completo')
            ->assertOk()
            ->assertJsonFragment([$product->nombre]);
    }

    public function test_search_suggestions_ignore_inactive_products(): void
    {
        $category = $this->createCategory();
        $this->createProduct($category, [
            'nombre' => 'Producto Invisible Test',
            'slug' => 'producto-invisible-test',
            'activo' => false,
        ]);

        $this->getJson('/buscar/sugerencias?q=Invisible')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_search_suggestions_ignore_products_from_inactive_stores(): void
    {
        $category = $this->createCategory();
        $inactiveStore = $this->createStore([
            'nombre' => 'Tienda Busqueda Inactiva',
            'slug' => 'tienda-busqueda-inactiva',
            'activa' => false,
        ]);
        $this->createProduct($category, [
            'tienda_id' => $inactiveStore->id,
            'nombre' => 'Producto Busqueda Oculto',
            'slug' => 'producto-busqueda-oculto',
        ]);

        $this->getJson('/buscar/sugerencias?q=Oculto')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_search_suggestions_require_at_least_two_characters(): void
    {
        $category = $this->createCategory();
        $this->createProduct($category, [
            'nombre' => 'Producto Breve Test',
            'slug' => 'producto-breve-test',
        ]);

        $this->getJson('/buscar/sugerencias?q=P')
            ->assertOk()
            ->assertExactJson([]);
    }

    private function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'nombre' => 'Categoria Test',
            'slug' => 'categoria-test',
            'orden' => 1,
            'activo' => true,
        ], $overrides));
    }

    private function createProduct(Category $category, array $overrides = []): Product
    {
        $storeId = $overrides['tienda_id'] ?? $this->createStore()->id;

        return Product::create(array_merge([
            'category_id' => $category->id,
            'tienda_id' => $storeId,
            'nombre' => 'Producto Test',
            'slug' => 'producto-test',
            'descripcion' => 'Descripcion test.',
            'precio' => 19990,
            'precio_oferta' => 14990,
            'stock' => 5,
            'imagen' => 'https://example.com/product.jpg',
            'envio_gratis' => true,
            'rating' => 4.5,
            'rating_count' => 10,
            'activo' => true,
            'estado' => 'nuevo',
        ], $overrides));
    }

    private function createStore(array $overrides = []): Tienda
    {
        $user = User::factory()->create();
        $user->assignRole('vendedor');

        return Tienda::create(array_merge([
            'user_id' => $user->id,
            'nombre' => 'Tienda Publica Test',
            'slug' => 'tienda-publica-test-'.uniqid(),
            'descripcion' => 'Descripcion tienda publica.',
            'activa' => true,
        ], $overrides));
    }
}
