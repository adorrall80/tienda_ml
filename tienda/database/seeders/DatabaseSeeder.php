<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────────
        $roleAdmin    = Role::create(['name' => 'admin']);
        $roleVendedor = Role::create(['name' => 'vendedor']);
        Role::create(['name' => 'cliente']);

        // ── Usuarios ──────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@tiendamv.cl',
            'password' => bcrypt('admin1234'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole($roleAdmin);

        $vendedor = User::create([
            'name'     => 'Vendedor Demo',
            'email'    => 'vendedor@tiendamv.cl',
            'password' => bcrypt('vendedor1234'),
            'email_verified_at' => now(),
        ]);
        $vendedor->assignRole($roleVendedor);

        $cliente = User::create([
            'name'     => 'Cliente Demo',
            'email'    => 'cliente@tiendamv.cl',
            'password' => bcrypt('cliente1234'),
            'email_verified_at' => now(),
        ]);
        $cliente->assignRole('cliente');

        // ── Tienda oficial (del admin) ────────────────────────────────
        $tiendaOficial = Tienda::create([
            'user_id'     => $admin->id,
            'nombre'      => 'TiendaMV Oficial',
            'slug'        => 'tiendamv-oficial',
            'descripcion' => 'Tienda oficial de TiendaMV con los mejores productos.',
            'activa'      => true,
        ]);

        // ── Tags ──────────────────────────────────────────────────────
        $oferta     = Tag::create(['nombre' => 'Oferta del día', 'slug' => 'oferta',      'label' => 'Oferta', 'color' => 'orange']);
        $masVendido = Tag::create(['nombre' => 'Más vendido',    'slug' => 'mas-vendido',  'label' => 'Top',    'color' => 'blue']);
        $nuevo      = Tag::create(['nombre' => 'Nuevo',          'slug' => 'nuevo',        'label' => 'Nuevo',  'color' => 'green']);
        $hot        = Tag::create(['nombre' => 'Hot',            'slug' => 'hot',          'label' => 'HOT',    'color' => 'red']);

        // ── Categorías ────────────────────────────────────────────────
        $electronica = Category::create(['nombre' => 'Electrónica', 'slug' => 'electronica', 'icono' => '📱', 'orden' => 1, 'activo' => true]);
        $ropa        = Category::create(['nombre' => 'Ropa y Moda', 'slug' => 'ropa',        'icono' => '👕', 'orden' => 2, 'activo' => true]);
        $hogar       = Category::create(['nombre' => 'Hogar',       'slug' => 'hogar',       'icono' => '🏠', 'orden' => 3, 'activo' => true]);
        $deportes    = Category::create(['nombre' => 'Deportes',    'slug' => 'deportes',    'icono' => '⚽', 'orden' => 4, 'activo' => true]);
        $automotriz  = Category::create(['nombre' => 'Automotriz',  'slug' => 'automotriz',  'icono' => '🚗', 'orden' => 5, 'activo' => true]);
        $juguetes    = Category::create(['nombre' => 'Juguetes',    'slug' => 'juguetes',    'icono' => '🧸', 'orden' => 6, 'activo' => true]);
        $inmuebles   = Category::create(['nombre' => 'Inmuebles',   'slug' => 'inmuebles',   'icono' => '🏢', 'orden' => 7, 'activo' => true]);
        Category::create(['nombre' => 'Servicios', 'slug' => 'servicios', 'icono' => '🔧', 'orden' => 8, 'activo' => true]);

        // ── Banners ───────────────────────────────────────────────────
        foreach ([
            ['badge' => 'Oferta del día',    'titulo' => "iPhone 15 Pro\n¡Solo por hoy!",     'subtitulo' => 'Con envío gratis a todo Chile',          'precio' => '$1.199.990',    'imagen' => 'https://picsum.photos/seed/iphone15/400/420', 'url' => '#', 'btn_texto' => 'Ver oferta',    'orden' => 1],
            ['badge' => 'Nuevo ingreso',     'titulo' => "Smart TV 55\"\nSamsung QLED",        'subtitulo' => 'Hasta 24 cuotas sin interés',             'precio' => '$599.990',      'imagen' => 'https://picsum.photos/seed/smarttv/400/420',  'url' => '#', 'btn_texto' => 'Ver TVs',       'orden' => 2],
            ['badge' => 'Cyber descuentos',  'titulo' => "Notebooks HP\ny Lenovo",             'subtitulo' => 'Hasta 40% de descuento esta semana',      'precio' => 'Desde $449.990','imagen' => 'https://picsum.photos/seed/notebook/400/420', 'url' => '#', 'btn_texto' => 'Ver notebooks', 'orden' => 3],
        ] as $b) {
            Banner::create($b);
        }

        // ── Productos: Ofertas del día ─────────────────────────────────
        $ofertas = [
            ['category_id' => $electronica->id, 'nombre' => 'Samsung Galaxy A54 5G 128GB Awesome Black',               'slug' => 'samsung-galaxy-a54-5g',        'precio' => 259990, 'precio_original' => 379990, 'stock' => 50,  'imagen' => 'https://picsum.photos/seed/phone1/300/300',   'envio_gratis' => true, 'cuotas' => 12, 'rating' => 5.00, 'rating_count' => 847,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Notebook HP 15 Intel Core i5 12va Gen 8GB RAM 512GB SSD', 'slug' => 'notebook-hp-15-i5-12va',       'precio' => 499990, 'precio_original' => 699990, 'stock' => 30,  'imagen' => 'https://picsum.photos/seed/laptop2/300/300',  'envio_gratis' => true, 'cuotas' => 24, 'rating' => 4.00, 'rating_count' => 312,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Apple AirPods Pro (2da Generación) con MagSafe',          'slug' => 'airpods-pro-2da-gen',           'precio' => 229990, 'precio_original' => 299990, 'stock' => 80,  'imagen' => 'https://picsum.photos/seed/airpods3/300/300', 'envio_gratis' => true, 'cuotas' => 12, 'rating' => 5.00, 'rating_count' => 1203, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Smart TV LG 50" 4K UHD ThinQ AI WebOS 23',                'slug' => 'smart-tv-lg-50-4k',             'precio' => 479990, 'precio_original' => 649990, 'stock' => 20,  'imagen' => 'https://picsum.photos/seed/tv4/300/300',      'envio_gratis' => true, 'cuotas' => 24, 'rating' => 4.00, 'rating_count' => 564,  'activo' => true],
            ['category_id' => $ropa->id,        'nombre' => 'Zapatillas Nike Air Max 270 Hombre Negro/Blanco',          'slug' => 'zapatillas-nike-air-max-270',   'precio' => 84990,  'precio_original' => 119990, 'stock' => 120, 'imagen' => 'https://picsum.photos/seed/shoes5/300/300',   'envio_gratis' => true, 'cuotas' => 6,  'rating' => 5.00, 'rating_count' => 2109, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Refrigerador Samsung 427L No Frost Twin Cooling Plus Inox','slug' => 'refrigerador-samsung-427l',    'precio' => 599990, 'precio_original' => 749990, 'stock' => 15,  'imagen' => 'https://picsum.photos/seed/fridge6/300/300',  'envio_gratis' => true, 'cuotas' => 24, 'rating' => 4.00, 'rating_count' => 389,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Sony PlayStation 5 825GB SSD Consola Standard Edition',   'slug' => 'sony-playstation-5',            'precio' => 699990, 'precio_original' => null,   'stock' => 10,  'imagen' => 'https://picsum.photos/seed/ps5game7/300/300', 'envio_gratis' => true, 'cuotas' => 24, 'rating' => 5.00, 'rating_count' => 4521, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'DJI Mini 3 Pro Drone con cámara 4K HDR hasta 34 min vuelo','slug' => 'dji-mini-3-pro',               'precio' => 899990, 'precio_original' => 1099990,'stock' => 8,   'imagen' => 'https://picsum.photos/seed/drone8/300/300',   'envio_gratis' => true, 'cuotas' => 24, 'rating' => 5.00, 'rating_count' => 218,  'activo' => true],
        ];

        foreach ($ofertas as $data) {
            $p = Product::create(array_merge($data, ['descripcion' => $data['nombre'] . '.', 'tienda_id' => $tiendaOficial->id]));
            $p->tags()->attach([$oferta->id]);
            $this->addExtraImages($p, $data['imagen']);
        }

        // ── Productos: Más vendidos ────────────────────────────────────
        $masVendidosData = [
            ['category_id' => $electronica->id, 'nombre' => 'Audífonos Sony WH-1000XM5 Noise Cancelling',      'slug' => 'audifonos-sony-wh-1000xm5',        'precio' => 259990, 'precio_original' => 305990, 'stock' => 60, 'imagen' => 'https://picsum.photos/seed/bestA/240/240', 'envio_gratis' => true, 'cuotas' => 12, 'rating' => 5.00, 'rating_count' => 1876, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Silla Gamer Ergonómica Reclinable RGB LED',        'slug' => 'silla-gamer-ergonomica-rgb',        'precio' => 149990, 'precio_original' => 192300, 'stock' => 40, 'imagen' => 'https://picsum.photos/seed/bestB/240/240', 'envio_gratis' => true, 'cuotas' => 12, 'rating' => 4.00, 'rating_count' => 3241, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Aspiradora Robot Xiaomi Mi Robot Vacuum S10',      'slug' => 'aspiradora-robot-xiaomi-s10',       'precio' => 299990, 'precio_original' => 333320, 'stock' => 25, 'imagen' => 'https://picsum.photos/seed/bestC/240/240', 'envio_gratis' => true, 'cuotas' => 12, 'rating' => 4.50, 'rating_count' => 2087, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Cafetera Nespresso Vertuo Pop Env120 Roja',         'slug' => 'cafetera-nespresso-vertuo-pop',     'precio' => 89990,  'precio_original' => null,   'stock' => 70, 'imagen' => 'https://picsum.photos/seed/bestD/240/240', 'envio_gratis' => true, 'cuotas' => 6,  'rating' => 4.50, 'rating_count' => 1543, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Monitor LG 27" 4K IPS UltraFine 144Hz DisplayHDR', 'slug' => 'monitor-lg-27-4k-ips',             'precio' => 449990, 'precio_original' => 549990, 'stock' => 18, 'imagen' => 'https://picsum.photos/seed/bestE/240/240', 'envio_gratis' => true, 'cuotas' => 24, 'rating' => 4.50, 'rating_count' => 965,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Samsung Galaxy Watch 6 Classic 47mm Negro',         'slug' => 'samsung-galaxy-watch-6-classic',   'precio' => 249990, 'precio_original' => 329990, 'stock' => 35, 'imagen' => 'https://picsum.photos/seed/bestF/240/240', 'envio_gratis' => true, 'cuotas' => 12, 'rating' => 4.00, 'rating_count' => 742,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Apple iPad 10ma Gen 64GB Wi-Fi Azul',               'slug' => 'apple-ipad-10-64gb',               'precio' => 489990, 'precio_original' => 599990, 'stock' => 22, 'imagen' => 'https://picsum.photos/seed/bestG/240/240', 'envio_gratis' => true, 'cuotas' => 24, 'rating' => 4.50, 'rating_count' => 1321, 'activo' => true],
        ];

        foreach ($masVendidosData as $data) {
            $p = Product::create(array_merge($data, ['descripcion' => $data['nombre'] . '.', 'tienda_id' => $tiendaOficial->id]));
            $p->tags()->attach([$masVendido->id]);
            $this->addExtraImages($p, $data['imagen']);
        }

        // Tags adicionales
        Product::where('slug', 'dji-mini-3-pro')->first()?->tags()->attach([$nuevo->id]);
        Product::where('slug', 'smart-tv-lg-50-4k')->first()?->tags()->attach([$nuevo->id]);
        Product::where('slug', 'notebook-hp-15-i5-12va')->first()?->tags()->attach([$hot->id]);
        Product::where('slug', 'sony-playstation-5')->first()?->tags()->attach([$hot->id]);
    }

    // Crea 3 imágenes adicionales en product_images usando el mismo seed base
    // pero con sufijos -b, -c, -d para que sean distintas a la principal
    private function addExtraImages(Product $product, string $mainUrl): void
    {
        preg_match('/seed\/([^\/]+)\//', $mainUrl, $m);
        $base = $m[1] ?? $product->slug;

        foreach (['-b', '-c', '-d'] as $orden => $suffix) {
            ProductImage::create([
                'product_id' => $product->id,
                'imagen'     => "https://picsum.photos/seed/{$base}{$suffix}/300/300",
                'orden'      => $orden + 1,
            ]);
        }
    }
}
