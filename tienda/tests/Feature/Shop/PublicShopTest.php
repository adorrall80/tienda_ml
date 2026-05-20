<?php

namespace Tests\Feature\Shop;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
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

    public function test_cart_page_can_be_rendered(): void
    {
        $this->get('/carrito')
            ->assertOk()
            ->assertSee('Mi carrito')
            ->assertSee('Resumen de compra');
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
        return Product::create(array_merge([
            'category_id' => $category->id,
            'nombre' => 'Producto Test',
            'slug' => 'producto-test',
            'descripcion' => 'Descripcion test.',
            'precio' => 19990,
            'precio_original' => 24990,
            'stock' => 5,
            'imagen' => 'https://example.com/product.jpg',
            'envio_gratis' => true,
            'cuotas' => 3,
            'rating' => 4.5,
            'rating_count' => 10,
            'activo' => true,
            'estado' => 'nuevo',
        ], $overrides));
    }
}
