<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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
            'pedidos' => Order::count(),
            'total_solicitado' => OrderItem::sum('total'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
