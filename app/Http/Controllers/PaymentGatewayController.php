<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\MomoService;
use App\Services\Payments\SepayService;
use App\Services\Payments\VnpayService;
use App\Services\Payments\ZalopayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Landing page shown right after checkout for any non-COD payment method.
 *
 * Shows the order (code, total, items) alongside the chosen gateway. If the
 * gateway has real merchant credentials configured in .env it hands off to
 * the actual provider; otherwise (the default out of the box) it runs a
 * local simulator so the order/payment lifecycle can still be exercised
 * end-to-end without a live merchant account.
 */
class PaymentGatewayController extends Controller
{
    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'payments']);
        $payment = $order->payments->sortByDesc('id')->first();

        abort_unless($payment, 404);

        if ($payment->status === Payment::STATUS_PAID) {
            return redirect()->route('checkout.success', $order);
        }

        // Allow retrying a failed attempt.
        if ($payment->status === Payment::STATUS_FAILED) {
            $payment->update(['status' => Payment::STATUS_PENDING]);
        }

        $method = $payment->method;

        if (! in_array($method, ['vnpay', 'momo', 'zalopay', 'sepay'], true)) {
            return redirect()->route('checkout.success', $order);
        }

        $demo = (bool) config("payment.{$method}.demo", true);
        $sepayQrUrl = null;

        if ($method === 'sepay' && ! $demo) {
            $sepayQrUrl = app(SepayService::class)->qrImageUrl($order);
        }

        return view('checkout.gateway', compact('order', 'payment', 'method', 'demo', 'sepayQrUrl'));
    }

    public function redirectVnpay(Order $order, Request $request, VnpayService $vnpay)
    {
        $payment = $this->pendingPayment($order, 'vnpay');
        abort_if(config('payment.vnpay.demo'), 404);

        return redirect()->away($vnpay->buildPaymentUrl($order, $request));
    }

    public function redirectMomo(Order $order, MomoService $momo)
    {
        $payment = $this->pendingPayment($order, 'momo');
        abort_if(config('payment.momo.demo'), 404);

        $result = $momo->createPayment($order);

        if (! $result['success']) {
            return redirect()->route('payment.gateway.show', $order)->with('error', $result['message']);
        }

        return redirect()->away($result['pay_url']);
    }

    public function redirectZalopay(Order $order, ZalopayService $zalopay)
    {
        $payment = $this->pendingPayment($order, 'zalopay');
        abort_if(config('payment.zalopay.demo'), 404);

        $result = $zalopay->createOrder($order);

        if (! $result['success']) {
            return redirect()->route('payment.gateway.show', $order)->with('error', $result['message']);
        }

        return redirect()->away($result['order_url']);
    }

    /**
     * Local simulator: lets the shopper (or a tester) mark a demo-mode
     * payment as paid/failed so the rest of the order lifecycle (status
     * updates, stock, coupon usage already applied at checkout) can be
     * exercised without a live gateway.
     */
    public function simulate(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $data = $request->validate([
            'outcome' => ['required', 'in:success,failed'],
        ]);

        $payment = $order->payments()->latest()->first();
        abort_unless($payment, 404);
        abort_unless($payment->status === Payment::STATUS_PENDING, 400);
        abort_unless((bool) config("payment.{$payment->method}.demo", true), 403, 'Cổng thanh toán này đang chạy ở chế độ thật, không thể giả lập.');

        $success = $data['outcome'] === 'success';

        $payment->update([
            'status' => $success ? Payment::STATUS_PAID : Payment::STATUS_FAILED,
            'transaction_ref' => $success ? 'DEMO'.now()->format('YmdHis') : null,
            'paid_at' => $success ? now() : null,
        ]);

        if ($success && $order->status === Order::STATUS_PENDING) {
            $order->update(['status' => Order::STATUS_CONFIRMED]);
        }

        return redirect()->route('checkout.success', $order)
            ->with($success ? 'success' : 'error', $success
                ? 'Thanh toán thành công (chế độ demo).'
                : 'Thanh toán thất bại (chế độ demo). Bạn có thể thử lại hoặc chọn phương thức khác.');
    }

    protected function pendingPayment(Order $order, string $method): Payment
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $payment = $order->payments()->where('method', $method)->latest()->first();
        abort_unless($payment && $payment->status === Payment::STATUS_PENDING, 404);

        return $payment;
    }
}
