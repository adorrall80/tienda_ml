<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Tienda;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        $tienda    = $request->user()->tienda;
        $productos = $tienda?->productos()->latest()->take(8)->get();

        return view('vendedor.dashboard', compact('tienda', 'productos'));
    }

    public function create()
    {
        return view('vendedor.tienda.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->tienda) {
            return redirect()->route('vendedor.dashboard');
        }

        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $base = Str::slug($data['nombre']);
        $slug = $base;
        $n    = 1;
        while (Tienda::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $n++;
        }

        Tienda::create([
            'user_id'     => $request->user()->id,
            'nombre'      => $data['nombre'],
            'slug'        => $slug,
            'descripcion' => $data['descripcion'] ?? null,
            'activa'      => true,
        ]);

        return redirect()->route('vendedor.dashboard')->with('success', '¡Tienda creada! Ya puedes agregar productos.');
    }

    public function edit(Request $request)
    {
        $tienda = $request->user()->tienda;

        if (! $tienda) {
            return redirect()->route('vendedor.tienda.create');
        }

        return view('vendedor.tienda.edit', compact('tienda'));
    }

    public function update(Request $request)
    {
        $tienda = $request->user()->tienda;

        abort_if(! $tienda, 404);

        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $tienda->update($data);

        return redirect()->route('vendedor.tienda.edit')->with('success', 'Tienda actualizada.');
    }
}
