<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DeliveryTypeController as AdminDeliveryType;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Admin\OrderStatusController as AdminOrderStatus;
use App\Http\Controllers\Admin\ProductConditionController as AdminProductCondition;
use App\Http\Controllers\Admin\ProductController as AdminProduct;
use App\Http\Controllers\Admin\SecurityBlockedTermController as AdminSecurityTerm;
use App\Http\Controllers\Admin\TiendaController as AdminTienda;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Auth\RedirectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductFavoriteController;
use App\Http\Controllers\Shop\ProductsController;
use App\Http\Controllers\Shop\SearchSuggestionsController;
use App\Http\Controllers\Vendedor\OrderController as VendedorOrder;
use App\Http\Controllers\Vendedor\ProductoController as VendedorProducto;
use App\Http\Controllers\Vendedor\TiendaController;
use Illuminate\Support\Facades\Route;

// ── Tienda pública ────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('inicio');
Route::get('/productos', [ProductsController::class, 'index'])->name('productos.index');
Route::get('/productos/{slug}', [ProductsController::class, 'show'])->name('productos.show');
Route::view('/carrito', 'shop.carrito')->name('carrito.index');
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmacion');
    Route::post('/productos/{producto}/favorito', [ProductFavoriteController::class, 'toggle'])->name('productos.favorito');
});
Route::get('/buscar/sugerencias', SearchSuggestionsController::class)
    ->middleware('throttle:60,1')
    ->name('buscar.sugerencias');

// ── Post-login redirect por rol ───────────────────────────────
Route::get('/redirect', RedirectController::class)->middleware('auth')->name('login.redirect');

// ── Mi cuenta (cliente) ───────────────────────────────────────
Route::middleware('auth')->prefix('mi-cuenta')->name('cuenta.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('perfil');
    Route::get('/compras/{order}', [ProfileController::class, 'showOrder'])->name('compras.show');
    Route::post('/quiero-vender', [ProfileController::class, 'becomeSeller'])->name('vendedor.activar');
    Route::patch('/', [ProfileController::class, 'update'])->name('perfil.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('perfil.destroy');
});

// ── Panel vendedor ────────────────────────────────────────────
Route::middleware(['auth', 'vendedor'])->prefix('mi-tienda')->name('vendedor.')->group(function () {
    Route::get('/', [TiendaController::class, 'index'])->name('panel');

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
    Route::get('/productos/{producto}/vista-previa', [VendedorProducto::class, 'preview'])->name('productos.preview');
    Route::get('/productos/{producto}/estado-revision', [VendedorProducto::class, 'reviewStatus'])->name('productos.estado-revision');
    Route::put('/productos/{producto}', [VendedorProducto::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [VendedorProducto::class, 'destroy'])->name('productos.destroy');
    Route::delete('/productos/{producto}/imagenes/{image}', [VendedorProducto::class, 'destroyImage'])->name('productos.imagenes.destroy');
    Route::patch('/productos/{producto}/imagenes/{image}/orden', [VendedorProducto::class, 'moveImage'])->name('productos.imagenes.orden');
    Route::patch('/productos/{producto}/toggle', [VendedorProducto::class, 'toggle'])->name('productos.toggle');

    // Pedidos recibidos
    Route::get('/pedidos', [VendedorOrder::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{order}', [VendedorOrder::class, 'show'])->name('pedidos.show');
    Route::patch('/pedidos/{order}/estado', [VendedorOrder::class, 'updateStatus'])->name('pedidos.estado');
    Route::post('/pedidos/{order}/notas', [VendedorOrder::class, 'storeNote'])->name('pedidos.notas');
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
    Route::get('/productos/{producto}/vista-previa', [AdminProduct::class, 'preview'])->name('productos.preview');
    Route::put('/productos/{producto}', [AdminProduct::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [AdminProduct::class, 'destroy'])->name('productos.destroy');
    Route::delete('/productos/{producto}/imagenes/{image}', [AdminProduct::class, 'destroyImage'])->name('productos.imagenes.destroy');
    Route::patch('/productos/{producto}/imagenes/{image}/orden', [AdminProduct::class, 'moveImage'])->name('productos.imagenes.orden');
    Route::patch('/productos/{producto}/toggle', [AdminProduct::class, 'toggle'])->name('productos.toggle');

    // Tiendas
    Route::get('/tiendas', [AdminTienda::class, 'index'])->name('tiendas.index');
    Route::get('/tiendas/{tienda}', [AdminTienda::class, 'show'])->name('tiendas.show');
    Route::patch('/tiendas/{tienda}/toggle', [AdminTienda::class, 'toggle'])->name('tiendas.toggle');

    // Pedidos
    Route::get('/pedidos', [AdminOrder::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{order}', [AdminOrder::class, 'show'])->name('pedidos.show');
    Route::patch('/pedidos/{order}/estado', [AdminOrder::class, 'updateStatus'])->name('pedidos.estado');
    Route::post('/pedidos/{order}/notas', [AdminOrder::class, 'storeNote'])->name('pedidos.notas');

    // Seguridad
    Route::get('/seguridad/palabras-bloqueadas', [AdminSecurityTerm::class, 'index'])->name('seguridad.palabras.index');
    Route::post('/seguridad/palabras-bloqueadas', [AdminSecurityTerm::class, 'store'])->name('seguridad.palabras.store');
    Route::put('/seguridad/palabras-bloqueadas/{term}', [AdminSecurityTerm::class, 'update'])->name('seguridad.palabras.update');
    Route::delete('/seguridad/palabras-bloqueadas/{term}', [AdminSecurityTerm::class, 'destroy'])->name('seguridad.palabras.destroy');

    // Mantenedores
    Route::get('/mantenedores/estados-producto', [AdminProductCondition::class, 'index'])->name('mantenedores.estados-producto.index');
    Route::post('/mantenedores/estados-producto', [AdminProductCondition::class, 'store'])->name('mantenedores.estados-producto.store');
    Route::put('/mantenedores/estados-producto/{condition}', [AdminProductCondition::class, 'update'])->name('mantenedores.estados-producto.update');
    Route::delete('/mantenedores/estados-producto/{condition}', [AdminProductCondition::class, 'destroy'])->name('mantenedores.estados-producto.destroy');
    Route::get('/mantenedores/estados-pedido', [AdminOrderStatus::class, 'index'])->name('mantenedores.estados-pedido.index');
    Route::post('/mantenedores/estados-pedido', [AdminOrderStatus::class, 'store'])->name('mantenedores.estados-pedido.store');
    Route::put('/mantenedores/estados-pedido/{status}', [AdminOrderStatus::class, 'update'])->name('mantenedores.estados-pedido.update');
    Route::delete('/mantenedores/estados-pedido/{status}', [AdminOrderStatus::class, 'destroy'])->name('mantenedores.estados-pedido.destroy');
    Route::get('/mantenedores/tipos-entrega', [AdminDeliveryType::class, 'index'])->name('mantenedores.tipos-entrega.index');
    Route::post('/mantenedores/tipos-entrega', [AdminDeliveryType::class, 'store'])->name('mantenedores.tipos-entrega.store');
    Route::put('/mantenedores/tipos-entrega/{deliveryType}', [AdminDeliveryType::class, 'update'])->name('mantenedores.tipos-entrega.update');
    Route::delete('/mantenedores/tipos-entrega/{deliveryType}', [AdminDeliveryType::class, 'destroy'])->name('mantenedores.tipos-entrega.destroy');
});

require __DIR__.'/auth.php';
