<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Support\Pricing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    private const STATUS_POOL = [
        Order::STATUS_DELIVERED,
        Order::STATUS_DELIVERED,
        Order::STATUS_DELIVERED,
        Order::STATUS_SHIPPED,
        Order::STATUS_PAID,
        Order::STATUS_PROCESSING,
        Order::STATUS_PENDING,
        Order::STATUS_CANCELLED,
        Order::STATUS_FAILED,
    ];

    public function run(): void
    {
        $products = Product::with('variants')->get();

        if ($products->isEmpty()) {
            return;
        }

        User::where('is_admin', false)->get()->each(function (User $user) use ($products) {
            $address = $user->addresses()->first();

            if (!$address) {
                return;
            }

            $orderCount = random_int(0, 4);

            for ($i = 0; $i < $orderCount; $i++) {
                $this->createOrder($user, $address, $products);
            }
        });
    }

    private function createOrder(User $user, $address, $products): void
    {
        $status = self::STATUS_POOL[array_rand(self::STATUS_POOL)];
        $createdAt = Carbon::now()->subDays(random_int(0, 90))->subHours(random_int(0, 23));

        $items = $products->random(min($products->count(), random_int(1, 3)));
        $subtotal = 0;
        $lineData = [];

        foreach ($items as $product) {
            $variant = $product->variants->isNotEmpty() ? $product->variants->random() : null;
            $qty = random_int(1, 2);
            $lineTotal = $product->price * $qty;
            $subtotal += $lineTotal;

            $lineData[] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'color_name' => $variant?->color_name,
                'image_url' => $product->main_image,
                'unit_price' => $product->price,
                'quantity' => $qty,
                'line_total' => $lineTotal,
            ];
        }

        $shipping = Pricing::shippingCostFor($subtotal);
        $discount = 0;
        $total = $subtotal + $shipping - $discount;

        $order = new Order([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $user->id,
            'status' => $status,
            'receiver_name' => $address->receiver_name,
            'receiver_mobile' => $address->receiver_mobile,
            'province' => $address->province,
            'city' => $address->city,
            'address_line' => $address->address_line,
            'postal_code' => $address->postal_code,
            'subtotal' => $subtotal,
            'discount_total' => $discount,
            'shipping_cost' => $shipping,
            'total' => $total,
            'payment_method' => 'local',
            'paid_at' => in_array($status, [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)
                ? (clone $createdAt)->addMinutes(random_int(2, 30))
                : null,
        ]);
        $order->timestamps = false;
        $order->created_at = $createdAt;
        $order->updated_at = $createdAt;
        $order->save();
        $order->timestamps = true;

        foreach ($lineData as $line) {
            OrderItem::create(array_merge(['order_id' => $order->id], $line));
        }

        $this->createPayment($order, $createdAt, $status);
    }

    private function createPayment(Order $order, Carbon $createdAt, string $status): void
    {
        $paymentStatus = match ($status) {
            Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED => Payment::STATUS_SUCCESS,
            Order::STATUS_FAILED => Payment::STATUS_FAILED,
            default => Payment::STATUS_PENDING,
        };

        $payment = new Payment([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'gateway' => 'local',
            'amount' => $order->total,
            'authority' => (string) random_int(1000000, 9999999),
            'ref_id' => $paymentStatus === Payment::STATUS_SUCCESS ? (string) random_int(100000, 999999) : null,
            'status' => $paymentStatus,
            'paid_at' => $order->paid_at,
        ]);
        $payment->timestamps = false;
        $payment->created_at = $createdAt;
        $payment->updated_at = $createdAt;
        $payment->save();
    }
}
