<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment as PaymentGateway;

class PaymentController extends Controller
{
    public function pay(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if ($order->isPaid()) {
            return redirect()->route('order.confirmation', $order);
        }

        $invoice = (new Invoice())->amount($order->total)->detail('order_id', $order->id);

        try {
            return PaymentGateway::callbackUrl(route('payment.callback', ['order_id' => $order->id]))
                ->purchase($invoice, function ($driver, $transactionId) use ($order) {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'gateway' => config('payment.default'),
                        'amount' => $order->total,
                        'authority' => $transactionId,
                        'status' => Payment::STATUS_PENDING,
                    ]);
                })
                ->pay()
                ->render();
        } catch (PurchaseFailedException $e) {
            Log::warning('Payment purchase failed', ['order_id' => $order->id, 'message' => $e->getMessage()]);

            return redirect()->route('checkout.index')->with('error', 'خطا در اتصال به درگاه پرداخت: '.$e->getMessage());
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $order = Order::findOrFail($request->query('order_id'));

        $payment = Payment::where('order_id', $order->id)->latest()->firstOrFail();

        if ($order->isPaid()) {
            return redirect()->route('order.confirmation', $order);
        }

        $transactionId = $request->input('transactionId') ?? $request->input('Authority');

        try {
            $receipt = PaymentGateway::amount($order->total)
                ->transactionId($transactionId ?: $payment->authority)
                ->verify();

            $payment->update([
                'status' => Payment::STATUS_SUCCESS,
                'ref_id' => $receipt->getReferenceId(),
                'payload' => $receipt->getDetails(),
                'paid_at' => now(),
            ]);

            $order->update([
                'status' => Order::STATUS_PROCESSING,
                'paid_at' => now(),
            ]);
        } catch (InvalidPaymentException $e) {
            $payment->update(['status' => Payment::STATUS_FAILED]);
            $order->update(['status' => Order::STATUS_FAILED]);
        }

        return redirect()->route('order.confirmation', $order);
    }

    public function confirmation(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'payment']);

        return view('order.confirmation', ['order' => $order]);
    }
}
