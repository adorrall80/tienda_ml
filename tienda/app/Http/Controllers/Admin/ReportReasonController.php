<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ReportReason;

class ReportReasonController extends Controller
{
    public function index()
    {
        $reasons = ReportReason::orderBy('id')->get();
        return view('admin.mantenedores.motivos-reporte', compact('reasons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'activo' => 'boolean'
        ]);

        ReportReason::create($data);

        return back()->with('success', 'Motivo creado exitosamente.');
    }

    public function update(Request $request, ReportReason $reason)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'activo' => 'boolean'
        ]);

        $reason->update($data);

        return back()->with('success', 'Motivo actualizado.');
    }

    public function destroy(ReportReason $reason)
    {
        $reason->delete();
        return back()->with('success', 'Motivo eliminado.');
    }
}
