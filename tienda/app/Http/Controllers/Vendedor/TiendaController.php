<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Tienda;
use App\Rules\NoReservedAttackWords;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        $tienda    = $request->user()->tienda;
        $productos = $tienda?->productos()->latest()->take(8)->get();
        $pedidosRecibidos = collect();
        $pedidosRecibidosCount = 0;
        $totalSolicitado = 0;

        if ($tienda) {
            $pedidosRecibidos = Order::query()
                ->whereHas('items', fn ($query) => $query->where('tienda_id', $tienda->id))
                ->with(['items' => fn ($query) => $query->where('tienda_id', $tienda->id), 'orderStatus'])
                ->latest()
                ->take(6)
                ->get();
            $pedidosRecibidosCount = Order::query()
                ->whereHas('items', fn ($query) => $query->where('tienda_id', $tienda->id))
                ->count();
            $totalSolicitado = OrderItem::query()
                ->where('tienda_id', $tienda->id)
                ->sum('total');
        }

        return view('vendedor.panel', compact('tienda', 'productos', 'pedidosRecibidos', 'pedidosRecibidosCount', 'totalSolicitado'));
    }

    public function create()
    {
        return view('vendedor.tienda.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->tienda) {
            return redirect()->route('vendedor.panel');
        }

        $data = $request->validate([
            'nombre'              => ['required', 'string', 'max:150', new NoReservedAttackWords],
            'descripcion'         => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'contacto_email'      => 'nullable|email|max:255',
            'contacto_telefono'   => 'nullable|string|max:50',
            'telefono_visible'     => 'boolean',
            'contacto_whatsapp'   => 'nullable|string|max:50',
            'permite_whatsapp'     => 'boolean',
            'contacto_direccion'  => ['nullable', 'string', 'max:255', new NoReservedAttackWords],
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
            'contacto_email' => $data['contacto_email'] ?? null,
            'contacto_telefono' => $data['contacto_telefono'] ?? null,
            'telefono_visible' => $request->has('telefono_visible') ? $request->boolean('telefono_visible') : true,
            'contacto_whatsapp' => $data['contacto_whatsapp'] ?? null,
            'permite_whatsapp' => $request->has('permite_whatsapp') ? $request->boolean('permite_whatsapp') : true,
            'contacto_direccion' => $data['contacto_direccion'] ?? null,
            'activa'      => true,
        ]);

        return redirect()->route('vendedor.panel')->with('success', '¡Tienda creada! Ya puedes agregar productos.');
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
            'nombre'              => ['required', 'string', 'max:150', new NoReservedAttackWords],
            'descripcion'         => ['nullable', 'string', 'max:500', new NoReservedAttackWords],
            'contacto_email'      => 'nullable|email|max:255',
            'contacto_telefono'   => 'nullable|string|max:50',
            'telefono_visible'     => 'boolean',
            'contacto_whatsapp'   => 'nullable|string|max:50',
            'permite_whatsapp'     => 'boolean',
            'contacto_direccion'  => ['nullable', 'string', 'max:255', new NoReservedAttackWords],
        ]);

        $data['telefono_visible'] = $request->has('telefono_visible')
            ? $request->boolean('telefono_visible')
            : $tienda->telefono_visible;
        $data['permite_whatsapp'] = $request->has('permite_whatsapp')
            ? $request->boolean('permite_whatsapp')
            : $tienda->permite_whatsapp;

        $tienda->update($data);

        return redirect()->route('vendedor.tienda.edit')->with('success', 'Tienda actualizada.');
    }
}
