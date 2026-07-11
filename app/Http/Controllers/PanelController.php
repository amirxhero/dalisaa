<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class PanelController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = $user->orders()->with('items')->get();

        $stats = [
            'orders_count' => $orders->count(),
            'paid_orders_count' => $orders->whereIn('status', [Order::STATUS_PAID, Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])->count(),
            'total_spent' => $orders->whereNotNull('paid_at')->sum('total'),
            'wishlist_count' => $user->wishlists()->count(),
            'addresses_count' => $user->addresses()->count(),
        ];

        $recentOrders = $orders->sortByDesc('created_at')->take(5);

        return view('panel.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('items')->paginate(8);

        return view('panel.orders', ['orders' => $orders]);
    }

    public function orderShow(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items.product', 'payment']);

        return view('panel.order-show', ['order' => $order]);
    }

    public function profile()
    {
        return view('panel.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'mobile' => ['required', 'string', 'regex:/^09[0-9]{9}$/', 'unique:users,mobile,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ], [], [
            'name' => 'نام و نام خانوادگی',
            'email' => 'ایمیل',
            'mobile' => 'شماره موبایل',
            'password' => 'رمز عبور',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'] ?? null;
        $user->mobile = $data['mobile'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        return back()->with('success', 'اطلاعات حساب کاربری با موفقیت به‌روزرسانی شد.');
    }
}
