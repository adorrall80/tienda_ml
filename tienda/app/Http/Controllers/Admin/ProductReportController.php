<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ProductReport;

class ProductReportController extends Controller
{
    public function index()
    {
        $reports = ProductReport::with(['product', 'user', 'reason'])
            ->orderBy('estado', 'asc') // pendiente first
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.reportes.index', compact('reports'));
    }

    public function blockProduct(Request $request, ProductReport $report)
    {
        $report->product->update(['bloqueado' => true]);
        $report->update(['estado' => 'revisado']);
        
        return back()->with('success', 'Producto bloqueado correctamente. El reporte ha sido marcado como revisado.');
    }

    public function dismiss(Request $request, ProductReport $report)
    {
        $report->update(['estado' => 'revisado']);
        return back()->with('success', 'Reporte marcado como revisado.');
    }
}
