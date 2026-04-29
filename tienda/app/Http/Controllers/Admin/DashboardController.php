<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'productos' => Product::count(),
            'usuarios'  => User::count(),
            'vendedores' => User::role('vendedor')->count(),
            'clientes'   => User::role('cliente')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
