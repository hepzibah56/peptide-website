<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $ordersToShip = Order::where('status', 'paid')->count()
            + Order::where('status', 'processing')->count();

        $awaitingPayment = Order::where('status', 'pending_payment')->count();

        $expiredThisWeek = Order::where('status', 'expired')
            ->where('placed_at', '>=', now()->subDays(7))
            ->count();

        $lowStock = Product::where('track_stock', true)
            ->where('stock', '<=', 10)
            ->where('stock', '>', 0)
            ->orderBy('stock')
            ->take(10)
            ->get();

        $outOfStock = Product::where('track_stock', true)
            ->where('stock', 0)
            ->count();

        $recentOrders = Order::latest()->take(10)->get();

        $thirtyDayRevenue = Order::whereNotIn('status', ['pending_payment', 'expired', 'cancelled'])
            ->where('placed_at', '>=', now()->subDays(30))
            ->sum('total_ore') / 100;

        return view('admin.dashboard', compact(
            'ordersToShip',
            'awaitingPayment',
            'expiredThisWeek',
            'lowStock',
            'outOfStock',
            'recentOrders',
            'thirtyDayRevenue'
        ));
    }
}