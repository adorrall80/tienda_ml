<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'vendedor']);
        Role::firstOrCreate(['name' => 'cliente']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.cl'],
            [
                'name' => 'Admin',
                'password' => bcrypt('123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        foreach ([
            ['nombre' => 'Electrónica', 'slug' => 'electronica', 'icono' => '📱', 'orden' => 1],
            ['nombre' => 'Ropa y Moda', 'slug' => 'ropa', 'icono' => '👕', 'orden' => 2],
            ['nombre' => 'Hogar', 'slug' => 'hogar', 'icono' => '🏠', 'orden' => 3],
            ['nombre' => 'Deportes', 'slug' => 'deportes', 'icono' => '⚽', 'orden' => 4],
            ['nombre' => 'Automotriz', 'slug' => 'automotriz', 'icono' => '🚗', 'orden' => 5],
            ['nombre' => 'Juguetes', 'slug' => 'juguetes', 'icono' => '🧸', 'orden' => 6],
            ['nombre' => 'Inmuebles', 'slug' => 'inmuebles', 'icono' => '🏢', 'orden' => 7],
            ['nombre' => 'Servicios', 'slug' => 'servicios', 'icono' => '🔧', 'orden' => 8],
        ] as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['activo' => true]
            );
        }

        foreach ([
            ['nombre' => 'Oferta del día', 'slug' => 'oferta', 'label' => 'Oferta', 'color' => 'orange'],
            ['nombre' => 'Más vendido', 'slug' => 'mas-vendido', 'label' => 'Top', 'color' => 'blue'],
            ['nombre' => 'Nuevo', 'slug' => 'nuevo', 'label' => 'Nuevo', 'color' => 'green'],
            ['nombre' => 'Hot', 'slug' => 'hot', 'label' => 'HOT', 'color' => 'red'],
        ] as $tag) {
            Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
