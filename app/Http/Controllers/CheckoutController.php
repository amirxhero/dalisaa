<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Support\Pricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function index()
    {
        $cart = $this->cartService->currentCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        $addresses = Auth::user()->addresses;
        $shipping = Pricing::shippingCostFor($cart->subtotal);

        return view('checkout.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'shipping' => $shipping,
            'total' => $cart->subtotal + $shipping,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->currentCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'سبد خرید شما خالی است.');
        }

        $data = $request->validate([
            'address_id' => ['nullable', 'exists:addresses,id'],
            'new_title' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'new_receiver_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'new_receiver_mobile' => ['required_without:address_id', 'nullable', 'regex:/^09[0-9]{9}$/'],
            'new_province' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'new_city' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'new_address_line' => ['required_without:address_id', 'nullable', 'string', 'max:1000'],
            'new_postal_code' => ['required_without:address_id', 'nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'new_title' => 'عنوان آدرس',
            'new_receiver_name' => 'نام گیرنده',
            'new_receiver_mobile' => 'موبایل گیرنده',
            'new_province' => 'استان',
            'new_city' => 'شهر',
            'new_address_line' => 'آدرس',
            'new_postal_code' => 'کد پستی',
        ]);

        $user = Auth::user();

        if (!empty($data['address_id'])) {
            $address = Address::where('user_id', $user->id)->findOrFail($data['address_id']);
        } else {
            $address = Address::create([
                'user_id' => $user->id,
                'title' => $data['new_title'],
                'receiver_name' => $data['new_receiver_name'],
                'receiver_mobile' => $data['new_receiver_mobile'],
                'province' => $data['new_province'],
                'city' => $data['new_city'],
                'address_line' => $data['new_address_line'],
                'postal_code' => $data['new_postal_code'],
                'is_default' => !$user->addresses()->exists(),
            ]);
        }

        $subtotal = 0;
        $lines = [];

        foreach ($cart->items as $item) {
            if (!$item->product->in_stock) {
                continue;
            }

            $lines[] = [
                'product_id' => $item->product_id,
                'title' => $item->product->title,
                'color_name' => $item->variant?->color_name,
                'image_url' => $item->product->main_thumb,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'line_total' => $item->line_total,
            ];

            $subtotal += $item->line_total;
        }

        if ($subtotal <= 0 || empty($lines)) {
            return redirect()->route('cart.index')->with('error', 'محصولات سبد خرید شما ناموجود شده‌اند.');
        }

        $shipping = Pricing::shippingCostFor($subtotal);

        $order = DB::transaction(function () use ($user, $address, $lines, $subtotal, $shipping, $data) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'status' => Order::STATUS_PENDING,
                'receiver_name' => $address->receiver_name,
                'receiver_mobile' => $address->receiver_mobile,
                'province' => $address->province,
                'city' => $address->city,
                'address_line' => $address->address_line,
                'postal_code' => $address->postal_code,
                'subtotal' => $subtotal,
                'discount_total' => 0,
                'shipping_cost' => $shipping,
                'total' => $subtotal + $shipping,
                'payment_method' => config('payment.default'),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line);
            }

            return $order;
        });

        $this->cartService->clear($cart);

        return redirect()->route('payment.pay', $order, 303);
    }
}
