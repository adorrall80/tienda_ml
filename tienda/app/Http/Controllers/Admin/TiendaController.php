<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tienda;

class TiendaController extends Controller
{
    public function index()
    {
        $tiendas = Tienda::with('user')
            ->withCount('productos')
            ->latest()
            ->paginate(20);

        return view('admin.tiendas.index', compact('tiendas'));
    }

    public function show(Tienda $tienda)
    {
        $tienda->load('user');
        $productos = $tienda->productos()->with('category')->latest()->paginate(15);

        return view('admin.tiendas.show', compact('tienda', 'productos'));
    }

    public function toggle(Tienda $tienda)
    {
        $tienda->update(['activa' => ! $tienda->activa]);

        return back()->with('success', $tienda->activa ? 'Tienda activada.' : 'Tienda desactivada.');
    }
}
