<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DeliveryType;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductCondition;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::updateOrCreate(
            ['id' => 1],
            ['name' => 'admin', 'guard_name' => 'web']
        );
        Role::updateOrCreate(
            ['id' => 2],
            ['name' => 'vendedor', 'guard_name' => 'web']
        );
        Role::updateOrCreate(
            ['id' => 3],
            ['name' => 'cliente', 'guard_name' => 'web']
        );

        $admin = User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Admin',
                'email' => 'admin@admin.cl',
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        foreach ([
            ['id' => 1, 'nombre' => 'Electrónica', 'slug' => 'electronica', 'icono' => '📱', 'orden' => 1],
            ['id' => 2, 'nombre' => 'Ropa y Moda', 'slug' => 'ropa', 'icono' => '👕', 'orden' => 2],
            ['id' => 3, 'nombre' => 'Hogar', 'slug' => 'hogar', 'icono' => '🏠', 'orden' => 3],
            ['id' => 4, 'nombre' => 'Deportes', 'slug' => 'deportes', 'icono' => '⚽', 'orden' => 4],
            ['id' => 5, 'nombre' => 'Automotriz', 'slug' => 'automotriz', 'icono' => '🚗', 'orden' => 5],
            ['id' => 6, 'nombre' => 'Juguetes', 'slug' => 'juguetes', 'icono' => '🧸', 'orden' => 6],
            ['id' => 7, 'nombre' => 'Inmuebles', 'slug' => 'inmuebles', 'icono' => '🏢', 'orden' => 7],
            ['id' => 8, 'nombre' => 'Servicios', 'slug' => 'servicios', 'icono' => '🔧', 'orden' => 8],
        ] as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                $category + ['activo' => true, 'parent_id' => null]
            );
        }

        foreach ([
            ['id' => 1, 'nombre' => 'Oferta del día', 'slug' => 'oferta', 'label' => 'Oferta', 'color' => 'orange'],
            ['id' => 2, 'nombre' => 'Más vendido', 'slug' => 'mas-vendido', 'label' => 'Top', 'color' => 'blue'],
            ['id' => 3, 'nombre' => 'Nuevo', 'slug' => 'nuevo', 'label' => 'Nuevo', 'color' => 'green'],
            ['id' => 4, 'nombre' => 'Hot', 'slug' => 'hot', 'label' => 'HOT', 'color' => 'red'],
        ] as $tag) {
            Tag::updateOrCreate(['id' => $tag['id']], $tag);
        }

        foreach ([
            ['id' => Product::ESTADO_NUEVO, 'nombre' => 'Nuevo', 'slug' => 'nuevo', 'orden' => 1, 'activo' => true],
            ['id' => Product::ESTADO_USADO, 'nombre' => 'Usado', 'slug' => 'usado', 'orden' => 2, 'activo' => true],
            ['id' => Product::ESTADO_REACONDICIONADO, 'nombre' => 'Reacondicionado', 'slug' => 'reacondicionado', 'orden' => 3, 'activo' => true],
        ] as $condition) {
            ProductCondition::updateOrCreate(['id' => $condition['id']], $condition);
        }

        foreach ([
            ['id' => 1, 'nombre' => 'Retiro en domicilio', 'slug' => 'retiro-en-domicilio', 'orden' => 1, 'activo' => true],
            ['id' => 2, 'nombre' => 'Delivery propio', 'slug' => 'delivery-propio', 'orden' => 2, 'activo' => true],
            ['id' => 3, 'nombre' => 'Envio por courier', 'slug' => 'envio-por-courier', 'orden' => 3, 'activo' => true],
        ] as $deliveryType) {
            DeliveryType::updateOrCreate(['id' => $deliveryType['id']], $deliveryType);
        }

        $orderStatusId = 1;
        foreach (Order::ESTADOS as $slug => $nombre) {
            OrderStatus::updateOrCreate(
                ['id' => $orderStatusId],
                [
                    'slug' => $slug,
                    'nombre' => $nombre,
                    'orden' => $orderStatusId,
                    'activo' => true,
                ]
            );
            $orderStatusId++;
        }
    }
}
