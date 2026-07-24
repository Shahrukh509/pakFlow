<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'pending' => Order::where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with('conversations')
            ->latest()
            ->paginate(15);

        return view('Dashboard.dashboard', compact('stats', 'recentOrders'));
    }
}