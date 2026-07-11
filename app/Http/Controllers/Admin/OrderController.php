<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private array $validStatuses = [
        Order::STATUS_PENDING,
        Order::STATUS_PROCESSING,
        Order::STATUS_PAID,
        Order::STATUS_SHIPPED,
        Order::STATUS_DELIVERED,
        Order::STATUS_CANCELLED,
        Order::STATUS_FAILED,
    ];

    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhere('receiver_mobile', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        $statusCounts = [];
        foreach ($this->validStatuses as $s) {
            $statusCounts[$s] = Order::where('status', $s)->count();
        }

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:'.implode(',', $this->validStatuses),
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }
}
