<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\TiendaController as AdminTienda;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductsController;
use App\Http\Controllers\Vendedor\ProductoController as VendedorProducto;
use App\Http\Controllers\Vendedor\TiendaController;
use Illuminate\Support\Facades\Route;

// ── Tienda pública ────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('inicio');
Route::get('/productos', [ProductsController::class, 'index'])->name('productos.index');
Route::get('/productos/{slug}', [ProductsController::class, 'show'])->name('productos.show');
Route::get('/carrito', fn() => abort(404))->name('carrito.index');
Route::get('/buscar/sugerencias', fn() => response()->json([]))->name('buscar.sugerencias');

// ── Post-login redirect por rol ───────────────────────────────
Route::get('/redirect', RedirectController::class)->middleware('auth')->name('login.redirect');

// ── Mi cuenta (cliente) ───────────────────────────────────────
Route::middleware('auth')->prefix('mi-cuenta')->name('cuenta.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('perfil');
    Route::patch('/', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('perfil.destroy');
});

// ── Panel vendedor ────────────────────────────────────────────
Route::middleware(['auth', 'vendedor'])->prefix('mi-tienda')->name('vendedor.')->group(function () {
    Route::get('/', [TiendaController::class, 'index'])->name('dashboard');

    // Tienda
    Route::get('/crear-tienda', [TiendaController::class, 'create'])->name('tienda.create');
    Route::post('/crear-tienda', [TiendaController::class, 'store'])->name('tienda.store');
    Route::get('/configuracion', [TiendaController::class, 'edit'])->name('tienda.edit');
    Route::put('/configuracion', [TiendaController::class, 'update'])->name('tienda.update');

    // Productos
    Route::get('/productos', [VendedorProducto::class, 'index'])->name('productos.index');
    Route::get('/productos/crear', [VendedorProducto::class, 'create'])->name('productos.create');
    Route::post('/productos', [VendedorProducto::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/editar', [VendedorProducto::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [VendedorProducto::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [VendedorProducto::class, 'destroy'])->name('productos.destroy');
    Route::patch('/productos/{producto}/toggle', [VendedorProducto::class, 'toggle'])->name('productos.toggle');
});

// ── Panel admin ───────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

    // Usuarios
    Route::get('/usuarios', [AdminUser::class, 'index'])->name('usuarios.index');
    Route::put('/usuarios/{user}', [AdminUser::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [AdminUser::class, 'destroy'])->name('usuarios.destroy');

    // Productos
    Route::get('/productos', [AdminProduct::class, 'index'])->name('productos.index');
    Route::get('/productos/crear', [AdminProduct::class, 'create'])->name('productos.create');
    Route::post('/productos', [AdminProduct::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/editar', [AdminProduct::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [AdminProduct::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [AdminProduct::class, 'destroy'])->name('productos.destroy');
    Route::patch('/productos/{producto}/toggle', [AdminProduct::class, 'toggle'])->name('productos.toggle');

    // Tiendas
    Route::get('/tiendas', [AdminTienda::class, 'index'])->name('tiendas.index');
    Route::get('/tiendas/{tienda}', [AdminTienda::class, 'show'])->name('tiendas.show');
    Route::patch('/tiendas/{tienda}/toggle', [AdminTienda::class, 'toggle'])->name('tiendas.toggle');
});

require __DIR__.'/auth.php';
