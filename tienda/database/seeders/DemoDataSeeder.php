<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\DeliveryType;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────────
        $roleAdmin    = Role::firstOrCreate(['name' => 'admin']);
        $roleVendedor = Role::firstOrCreate(['name' => 'vendedor']);
        Role::firstOrCreate(['name' => 'cliente']);

        // ── Usuarios ──────────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.cl'],
            [
                'name'     => 'Admin',
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        $vendedor = User::updateOrCreate(
            ['email' => 'vendedor@tiendamv.cl'],
            [
                'name'     => 'Vendedor Demo',
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]
        );
        $vendedor->syncRoles([$roleVendedor]);

        $cliente = User::updateOrCreate(
            ['email' => 'cliente@tiendamv.cl'],
            [
                'name'     => 'Cliente Demo',
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]
        );
        $cliente->syncRoles(['cliente']);

        // ── Tienda oficial (del admin) ────────────────────────────────
        $tiendaOficial = Tienda::updateOrCreate(
            ['slug' => 'tiendamv-oficial'],
            [
                'user_id'     => $admin->id,
                'nombre'      => 'TiendaMV Oficial',
                'descripcion' => 'Tienda oficial de TiendaMV con los mejores productos.',
                'contacto_email' => 'ventas@tiendamv.cl',
                'contacto_telefono' => '+56 2 2345 6789',
                'contacto_whatsapp' => '+56 9 8765 4321',
                'contacto_direccion' => 'Av. Providencia 1234, Santiago',
                'activa'      => true,
            ]
        );

        $tiendaNorte = Tienda::updateOrCreate(
            ['slug' => 'tienda-norte-demo'],
            [
                'user_id'     => $vendedor->id,
                'nombre'      => 'Tienda Norte Demo',
                'descripcion' => 'Productos demo de otra tienda para pruebas multi-vendedor.',
                'contacto_email' => 'ventas@nortedemo.cl',
                'contacto_telefono' => '+56 2 2222 3333',
                'contacto_whatsapp' => '+56 9 2222 3333',
                'contacto_direccion' => 'Av. Norte 456, Santiago',
                'activa'      => true,
            ]
        );

        // ── Tags ──────────────────────────────────────────────────────
        $oferta     = Tag::updateOrCreate(['slug' => 'oferta'], ['nombre' => 'Oferta del día', 'label' => 'Oferta', 'color' => 'orange']);
        $masVendido = Tag::updateOrCreate(['slug' => 'mas-vendido'], ['nombre' => 'Más vendido', 'label' => 'Top', 'color' => 'blue']);
        $nuevo      = Tag::updateOrCreate(['slug' => 'nuevo'], ['nombre' => 'Nuevo', 'label' => 'Nuevo', 'color' => 'green']);
        $hot        = Tag::updateOrCreate(['slug' => 'hot'], ['nombre' => 'Hot', 'label' => 'HOT', 'color' => 'red']);

        // ── Categorías ────────────────────────────────────────────────
        $electronica = Category::updateOrCreate(['slug' => 'electronica'], ['nombre' => 'Electrónica', 'icono' => '📱', 'orden' => 1, 'activo' => true]);
        $ropa        = Category::updateOrCreate(['slug' => 'ropa'], ['nombre' => 'Ropa y Moda', 'icono' => '👕', 'orden' => 2, 'activo' => true]);
        $hogar       = Category::updateOrCreate(['slug' => 'hogar'], ['nombre' => 'Hogar', 'icono' => '🏠', 'orden' => 3, 'activo' => true]);
        $deportes    = Category::updateOrCreate(['slug' => 'deportes'], ['nombre' => 'Deportes', 'icono' => '⚽', 'orden' => 4, 'activo' => true]);
        $automotriz  = Category::updateOrCreate(['slug' => 'automotriz'], ['nombre' => 'Automotriz', 'icono' => '🚗', 'orden' => 5, 'activo' => true]);
        $juguetes    = Category::updateOrCreate(['slug' => 'juguetes'], ['nombre' => 'Juguetes', 'icono' => '🧸', 'orden' => 6, 'activo' => true]);
        $inmuebles   = Category::updateOrCreate(['slug' => 'inmuebles'], ['nombre' => 'Inmuebles', 'icono' => '🏢', 'orden' => 7, 'activo' => true]);
        Category::updateOrCreate(['slug' => 'servicios'], ['nombre' => 'Servicios', 'icono' => '🔧', 'orden' => 8, 'activo' => true]);

        // ── Banners ───────────────────────────────────────────────────
        foreach ([
            ['badge' => 'Oferta del día',    'titulo' => "iPhone 15 Pro\n¡Solo por hoy!",     'subtitulo' => 'Con envío gratis a todo Chile',          'precio' => '$1.199.990',    'imagen' => 'https://picsum.photos/seed/iphone15/400/420', 'url' => '#', 'btn_texto' => 'Ver oferta',    'orden' => 1],
            ['badge' => 'Nuevo ingreso',     'titulo' => "Smart TV 55\"\nSamsung QLED",        'subtitulo' => 'Coordina directo con la tienda',          'precio' => '$599.990',      'imagen' => 'https://picsum.photos/seed/smarttv/400/420',  'url' => '#', 'btn_texto' => 'Ver TVs',       'orden' => 2],
            ['badge' => 'Cyber descuentos',  'titulo' => "Notebooks HP\ny Lenovo",             'subtitulo' => 'Hasta 40% de descuento esta semana',      'precio' => 'Desde $449.990','imagen' => 'https://picsum.photos/seed/notebook/400/420', 'url' => '#', 'btn_texto' => 'Ver notebooks', 'orden' => 3],
        ] as $b) {
            Banner::updateOrCreate(['orden' => $b['orden']], $b);
        }

        // ── Productos: Ofertas del día ─────────────────────────────────
        $ofertas = [
            ['category_id' => $electronica->id, 'nombre' => 'Samsung Galaxy A54 5G 128GB Awesome Black',               'slug' => 'samsung-galaxy-a54-5g',        'sku' => 'SL00998', 'descripcion_corta' => 'Telefono nuevo liberado con buena camara y bateria.', 'descripcion' => '<p>Telefono nuevo liberado para uso diario, con pantalla amplia, buena autonomia y camara versatil.</p><ul><li>128GB de almacenamiento</li><li>Compatible con redes 5G</li><li>Ideal para fotos, redes sociales y trabajo movil</li></ul>', 'precio' => 379990,  'precio_oferta' => 259990, 'stock' => 50,  'imagen' => 'https://picsum.photos/seed/phone1/300/300',   'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 847,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Notebook HP 15 Intel Core i5 12va Gen 8GB RAM 512GB SSD', 'slug' => 'notebook-hp-15-i5-12va',       'sku' => '000001', 'descripcion_corta' => 'Notebook para estudio, oficina y navegacion diaria.', 'descripcion' => '<p>Equipo liviano para productividad, clases online y uso de oficina.</p><ul><li>Procesador Intel Core i5</li><li>Disco SSD rapido</li><li>Buen equilibrio entre rendimiento y precio</li></ul>', 'precio' => 699990,  'precio_oferta' => 499990, 'stock' => 30,  'imagen' => 'https://picsum.photos/seed/laptop2/300/300',  'envio_gratis' => true, 'rating' => 4.00, 'rating_count' => 312,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Apple AirPods Pro (2da Generación) con MagSafe',          'slug' => 'airpods-pro-2da-gen',           'sku' => 'AUD002', 'descripcion_corta' => 'Audifonos con cancelacion de ruido y estuche MagSafe.', 'descripcion' => '<p>Audifonos compactos para llamadas, musica y concentracion.</p><ul><li>Cancelacion activa de ruido</li><li>Modo ambiente</li><li>Estuche de carga compatible con MagSafe</li></ul>', 'precio' => 299990,  'precio_oferta' => 229990, 'stock' => 80,  'imagen' => 'https://picsum.photos/seed/airpods3/300/300', 'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 1203, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Smart TV LG 50" 4K UHD ThinQ AI WebOS 23',                'slug' => 'smart-tv-lg-50-4k',             'sku' => 'TV0004', 'descripcion_corta' => 'Smart TV 4K para streaming, peliculas y consola.', 'descripcion' => '<p>Televisor nuevo con resolucion 4K y sistema WebOS para acceder rapido a tus aplicaciones favoritas.</p><ul><li>Pantalla de 50 pulgadas</li><li>Resolucion UHD</li><li>Control inteligente ThinQ AI</li></ul>', 'precio' => 649990,  'precio_oferta' => 479990, 'stock' => 20,  'imagen' => 'https://picsum.photos/seed/tv4/300/300',      'envio_gratis' => true, 'rating' => 4.00, 'rating_count' => 564,  'activo' => true],
            ['category_id' => $ropa->id,        'nombre' => 'Zapatillas Nike Air Max 270 Hombre Negro/Blanco',          'slug' => 'zapatillas-nike-air-max-270',   'descripcion_corta' => 'Producto nuevo: zapatillas urbanas para uso diario.', 'descripcion' => '<p>Producto nuevo de estilo urbano, comodo para caminar y combinar con looks casuales.</p><ul><li>Color negro/blanco</li><li>Camara Air visible</li><li>Disponibles para coordinar retiro o entrega</li></ul>', 'precio' => 119990,  'precio_oferta' => 84990,  'stock' => 120, 'imagen' => 'https://picsum.photos/seed/shoes5/300/300',   'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 2109, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Refrigerador Samsung 427L No Frost Twin Cooling Plus Inox','slug' => 'refrigerador-samsung-427l',    'descripcion_corta' => 'Refrigerador No Frost amplio para cocina familiar.', 'descripcion' => '<p>Refrigerador de gran capacidad para mantener alimentos frescos y organizados.</p><ul><li>Sistema No Frost</li><li>Terminacion inox</li><li>Espacio amplio para familia</li></ul>', 'precio' => 749990,  'precio_oferta' => 599990, 'stock' => 15,  'imagen' => 'https://picsum.photos/seed/fridge6/300/300',  'envio_gratis' => true, 'rating' => 4.00, 'rating_count' => 389,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Sony PlayStation 5 825GB SSD Consola Standard Edition',   'slug' => 'sony-playstation-5',            'descripcion_corta' => 'Consola PS5 nueva con SSD rapido.', 'descripcion' => '<p>Consola de videojuegos para disfrutar titulos de nueva generacion con carga rapida.</p><ul><li>SSD de 825GB</li><li>Edicion standard</li><li>Compatible con juegos fisicos y digitales</li></ul>', 'precio' => 699990,  'precio_oferta' => null,   'stock' => 10,  'imagen' => 'https://picsum.photos/seed/ps5game7/300/300', 'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 4521, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'DJI Mini 3 Pro Drone con cámara 4K HDR hasta 34 min vuelo','slug' => 'dji-mini-3-pro',               'descripcion_corta' => 'Drone compacto con camara 4K para vuelos recreativos.', 'descripcion' => '<p>Drone compacto para capturar video 4K HDR en paseos, eventos y contenido creativo.</p><ul><li>Camara 4K HDR</li><li>Hasta 34 minutos de vuelo</li><li>Formato liviano y facil de transportar</li></ul>', 'precio' => 1099990, 'precio_oferta' => 899990, 'stock' => 8,   'imagen' => 'https://picsum.photos/seed/drone8/300/300',   'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 218,  'activo' => true],
        ];

        foreach ($ofertas as $data) {
            $p = Product::firstOrCreate(['slug' => $data['slug']], array_merge($data, ['tienda_id' => $tiendaOficial->id]));
            if (in_array($p->slug, ['samsung-galaxy-a54-5g', 'sony-playstation-5'], true)) {
                $p->update(['destacado' => true]);
            }
            $p->update([
                'retiro_en_domicilio' => true,
                'delivery' => true,
                'envio_courier' => true,
                'costo_envio' => $p->envio_gratis ? null : 3990,
                'tiempo_entrega' => '2 a 3 días hábiles',
            ]);
            $this->syncDemoDeliveryTypes($p);
            $p->tags()->syncWithoutDetaching([$oferta->id]);
            $this->addExtraImages($p, $data['imagen']);
            $this->addDemoAttributes($p);
        }

        // ── Productos: Más vendidos ────────────────────────────────────
        $masVendidosData = [
            ['category_id' => $electronica->id, 'nombre' => 'Audífonos Sony WH-1000XM5 Noise Cancelling',      'slug' => 'audifonos-sony-wh-1000xm5',        'descripcion_corta' => 'Audifonos premium con cancelacion de ruido.', 'descripcion' => '<p>Audifonos para trabajo, viajes y musica con sonido claro y cancelacion avanzada.</p><ul><li>Cancelacion de ruido</li><li>Bateria de larga duracion</li><li>Conexion Bluetooth</li></ul>', 'precio' => 305990, 'precio_oferta' => 259990, 'stock' => 60, 'imagen' => 'https://picsum.photos/seed/bestA/240/240', 'envio_gratis' => true, 'rating' => 5.00, 'rating_count' => 1876, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Silla Gamer Ergonómica Reclinable RGB LED',        'slug' => 'silla-gamer-ergonomica-rgb',        'descripcion_corta' => 'Silla reclinable para escritorio o setup gamer.', 'descripcion' => '<p>Silla comoda para largas jornadas frente al computador.</p><ul><li>Respaldo reclinable</li><li>Diseño ergonomico</li><li>Iluminacion RGB decorativa</li></ul>', 'precio' => 192300, 'precio_oferta' => 149990, 'stock' => 40, 'imagen' => 'https://picsum.photos/seed/bestB/240/240', 'envio_gratis' => true, 'rating' => 4.00, 'rating_count' => 3241, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Aspiradora Robot Xiaomi Mi Robot Vacuum S10',      'slug' => 'aspiradora-robot-xiaomi-s10',       'descripcion_corta' => 'Aspiradora robot para limpieza automatica del hogar.', 'descripcion' => '<p>Aspiradora robot para mantener pisos limpios con menor esfuerzo diario.</p><ul><li>Navegacion inteligente</li><li>Programacion desde app</li><li>Ideal para departamentos y casas</li></ul>', 'precio' => 333320, 'precio_oferta' => 299990, 'stock' => 25, 'imagen' => 'https://picsum.photos/seed/bestC/240/240', 'envio_gratis' => true, 'rating' => 4.50, 'rating_count' => 2087, 'activo' => true],
            ['category_id' => $hogar->id,       'nombre' => 'Cafetera Nespresso Vertuo Pop Env120 Roja',         'slug' => 'cafetera-nespresso-vertuo-pop',     'descripcion_corta' => 'Cafetera compacta para preparar cafe en casa.', 'descripcion' => '<p>Cafetera compacta de color rojo para preparar cafe rapido y con buen acabado.</p><ul><li>Formato de capsulas</li><li>Diseno compacto</li><li>Ideal para cocina u oficina</li></ul>', 'precio' => 89990,  'precio_oferta' => null,   'stock' => 70, 'imagen' => 'https://picsum.photos/seed/bestD/240/240', 'envio_gratis' => true, 'rating' => 4.50, 'rating_count' => 1543, 'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Monitor LG 27" 4K IPS UltraFine 144Hz DisplayHDR', 'slug' => 'monitor-lg-27-4k-ips',             'descripcion_corta' => 'Monitor 4K para trabajo, diseno y entretenimiento.', 'descripcion' => '<p>Monitor amplio con alta resolucion para escritorio productivo y contenido multimedia.</p><ul><li>Panel IPS de 27 pulgadas</li><li>Resolucion 4K</li><li>Frecuencia 144Hz</li></ul>', 'precio' => 549990, 'precio_oferta' => 449990, 'stock' => 18, 'imagen' => 'https://picsum.photos/seed/bestE/240/240', 'envio_gratis' => true, 'rating' => 4.50, 'rating_count' => 965,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Samsung Galaxy Watch 6 Classic 47mm Negro',         'slug' => 'samsung-galaxy-watch-6-classic',   'descripcion_corta' => 'Reloj inteligente para actividad y notificaciones.', 'descripcion' => '<p>Smartwatch para monitorear actividad, recibir notificaciones y complementar el telefono.</p><ul><li>Caja de 47mm</li><li>Color negro</li><li>Funciones deportivas y de salud</li></ul>', 'precio' => 329990, 'precio_oferta' => 249990, 'stock' => 35, 'imagen' => 'https://picsum.photos/seed/bestF/240/240', 'envio_gratis' => true, 'rating' => 4.00, 'rating_count' => 742,  'activo' => true],
            ['category_id' => $electronica->id, 'nombre' => 'Apple iPad 10ma Gen 64GB Wi-Fi Azul',               'slug' => 'apple-ipad-10-64gb',               'descripcion_corta' => 'Tablet Wi-Fi para estudio, entretencion y notas.', 'descripcion' => '<p>Tablet liviana para estudiar, navegar, ver contenido y tomar notas.</p><ul><li>64GB de almacenamiento</li><li>Conexion Wi-Fi</li><li>Color azul</li></ul>', 'precio' => 599990, 'precio_oferta' => 489990, 'stock' => 22, 'imagen' => 'https://picsum.photos/seed/bestG/240/240', 'envio_gratis' => true, 'rating' => 4.50, 'rating_count' => 1321, 'activo' => true],
        ];

        foreach ($masVendidosData as $data) {
            $p = Product::firstOrCreate(['slug' => $data['slug']], array_merge($data, ['tienda_id' => $tiendaOficial->id]));
            if (in_array($p->slug, ['audifonos-sony-wh-1000xm5', 'apple-ipad-10-64gb'], true)) {
                $p->update(['destacado' => true]);
            }
            $p->update([
                'retiro_en_domicilio' => true,
                'delivery' => false,
                'envio_courier' => true,
                'costo_envio' => $p->envio_gratis ? null : 2990,
                'tiempo_entrega' => 'Coordinar con la tienda',
            ]);
            $this->syncDemoDeliveryTypes($p);
            $p->tags()->syncWithoutDetaching([$masVendido->id]);
            $this->addExtraImages($p, $data['imagen']);
            $this->addDemoAttributes($p);
        }

        $productosNorte = [
            ['nombre' => 'Lámpara LED Tienda Norte Demo', 'slug' => 'lampara-led-tienda-norte-demo', 'precio' => 35990, 'stock' => 12, 'imagen' => 'https://picsum.photos/seed/lampara-norte-demo/300/300', 'rating_count' => 18],
            ['nombre' => 'Mesa Auxiliar Tienda Norte Demo', 'slug' => 'mesa-auxiliar-tienda-norte-demo', 'precio' => 45990, 'stock' => 9, 'imagen' => 'https://picsum.photos/seed/mesa-norte-demo/300/300', 'rating_count' => 11],
            ['nombre' => 'Organizador Hogar Tienda Norte Demo', 'slug' => 'organizador-hogar-tienda-norte-demo', 'precio' => 22990, 'stock' => 15, 'imagen' => 'https://picsum.photos/seed/organizador-norte-demo/300/300', 'rating_count' => 7],
        ];

        foreach ($productosNorte as $data) {
            $productoNorte = Product::firstOrCreate(['slug' => $data['slug']], [
                'category_id' => $hogar->id,
                'tienda_id' => $tiendaNorte->id,
                'nombre' => $data['nombre'],
                'descripcion' => 'Producto demo para probar compras con tiendas distintas.',
                'precio' => $data['precio'],
                'precio_oferta' => null,
                'stock' => $data['stock'],
                'imagen' => $data['imagen'],
                'envio_gratis' => true,
                'rating' => 4.50,
                'rating_count' => $data['rating_count'],
                'activo' => true,
                'estado_id' => Product::ESTADO_NUEVO,
                'retiro_en_domicilio' => true,
                'delivery' => true,
                'envio_courier' => false,
                'costo_envio' => null,
                'tiempo_entrega' => '24 a 48 horas',
            ]);
            $productoNorte->tags()->syncWithoutDetaching([$nuevo->id]);
            $this->syncDemoDeliveryTypes($productoNorte);
            $this->addExtraImages($productoNorte, $productoNorte->imagen);
            $this->addDemoAttributes($productoNorte);
        }

        // Tags adicionales
        Product::where('slug', 'dji-mini-3-pro')->first()?->tags()->syncWithoutDetaching([$nuevo->id]);
        Product::where('slug', 'smart-tv-lg-50-4k')->first()?->tags()->syncWithoutDetaching([$nuevo->id]);
        Product::where('slug', 'notebook-hp-15-i5-12va')->first()?->tags()->syncWithoutDetaching([$hot->id]);
        Product::where('slug', 'sony-playstation-5')->first()?->tags()->syncWithoutDetaching([$hot->id]);
    }

    // Crea 3 imágenes adicionales en product_images usando el mismo seed base
    // pero con sufijos -b, -c, -d para que sean distintas a la principal
    private function addExtraImages(Product $product, string $mainUrl): void
    {
        preg_match('/seed\/([^\/]+)\//', $mainUrl, $m);
        $base = $m[1] ?? $product->slug;

        foreach (['-b', '-c', '-d'] as $orden => $suffix) {
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'orden' => $orden + 1],
                ['imagen' => "https://picsum.photos/seed/{$base}{$suffix}/300/300"]
            );
        }
    }

    private function addDemoAttributes(Product $product): void
    {
        if ($product->productAttributes()->exists()) {
            return;
        }

        $attributes = match (true) {
            str_contains($product->slug, 'zapatillas') => [
                ['nombre' => 'Marca', 'valor' => 'Nike'],
                ['nombre' => 'Color', 'valor' => 'Negro/Blanco'],
                ['nombre' => 'Talla', 'valor' => 'Varias tallas'],
            ],
            str_contains($product->slug, 'refrigerador') => [
                ['nombre' => 'Marca', 'valor' => 'Samsung'],
                ['nombre' => 'Capacidad', 'valor' => '427L'],
                ['nombre' => 'Sistema', 'valor' => 'No Frost'],
            ],
            str_contains($product->slug, 'silla') => [
                ['nombre' => 'Material', 'valor' => 'Eco cuero'],
                ['nombre' => 'Uso', 'valor' => 'Escritorio'],
                ['nombre' => 'Color', 'valor' => 'Negro'],
            ],
            default => [
                ['nombre' => 'Marca', 'valor' => explode(' ', $product->nombre)[0] ?? 'Demo'],
                ['nombre' => 'Estado', 'valor' => 'Nuevo'],
                ['nombre' => 'Origen', 'valor' => 'Demo'],
            ],
        };

        foreach ($attributes as $index => $attribute) {
            $product->productAttributes()->create([
                'nombre' => $attribute['nombre'],
                'valor' => $attribute['valor'],
                'orden' => $index + 1,
            ]);
        }
    }

    private function syncDemoDeliveryTypes(Product $product): void
    {
        $slugs = collect([
            $product->retiro_en_domicilio ? 'retiro-en-domicilio' : null,
            $product->delivery ? 'delivery-propio' : null,
            $product->envio_courier ? 'envio-por-courier' : null,
        ])->filter()->all();

        $ids = DeliveryType::whereIn('slug', $slugs)->pluck('id')->all();
        $product->deliveryTypes()->sync($ids);
    }

}
