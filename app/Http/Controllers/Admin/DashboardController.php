<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CurrencyService;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue'    => Order::whereNotNull('paid_at')->sum('total'),
            'orders_count'     => Order::count(),
            'pending_orders'   => Order::whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->count(),
            'products_count'   => Product::count(),
            'active_products'  => Product::where('is_active', true)->count(),
            'users_count'      => User::where('is_admin', false)->count(),
            'discounts_active' => DiscountCode::where('is_active', true)->count(),
            'low_stock'        => Product::where('stock', '<', 10)->where('is_active', true)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(8)
            ->get();

        $lowStockProducts = Product::where('stock', '<', 10)
            ->where('is_active', true)
            ->with('category')
            ->orderBy('stock')
            ->take(5)
            ->get();

        $rates = CurrencyService::allRates();

        $orderStatusCounts = [];
        foreach (Order::STATUS_LABELS as $key => $label) {
            $orderStatusCounts[$key] = Order::where('status', $key)->count();
        }

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts', 'rates', 'orderStatusCounts'));
    }
}
