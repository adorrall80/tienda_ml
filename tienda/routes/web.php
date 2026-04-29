<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductsController;
use Illuminate\Support\Facades\Route;

// Tienda
Route::get('/', [HomeController::class, 'index'])->name('inicio');
Route::get('/productos', [ProductsController::class, 'index'])->name('productos.index');
Route::get('/productos/{slug}', [ProductsController::class, 'show'])->name('productos.show');
Route::get('/carrito', fn() => abort(404))->name('carrito.index');
Route::get('/buscar/sugerencias', fn() => response()->json([]))->name('buscar.sugerencias');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
