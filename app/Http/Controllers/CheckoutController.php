<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Shipment;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();
        $addresses = Auth::user()->addresses()->orderByDesc('is_default')->get();

        return view('checkout.index', compact('items', 'subtotal', 'addresses'));
    }

    public function applyCoupon(Request $request)
    {
        $data = $request->validate(['code' => ['required', 'string']]);

        $coupon = Coupon::where('code', $data['code'])->first();
        $subtotal = $this->cart->subtotal();

        if (! $coupon || ! $coupon->isValidNow()) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        if ($coupon->calculateDiscount($subtotal) <= 0) {
            return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu để áp dụng mã này.');
        }

        session(['checkout_coupon' => $coupon->code]);

        return back()->with('success', "Áp dụng mã {$coupon->code} thành công.");
    }

    public function removeCoupon()
    {
        session()->forget('checkout_coupon');

        return back()->with('success', 'Đã bỏ mã giảm giá.');
    }

    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $data = $request->validate([
            'ship_recipient' => ['required', 'string', 'max:150'],
            'ship_phone' => ['required', 'string', 'max:20'],
            'ship_address' => ['required', 'string', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,bank_transfer,vnpay,momo,zalopay,sepay'],
        ]);

        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        $coupon = null;
        $discount = 0;
        if ($code = session('checkout_coupon')) {
            $coupon = Coupon::where('code', $code)->first();
            if ($coupon && $coupon->isValidNow()) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                $coupon = null;
            }
        }

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        $total = max($subtotal - $discount + $shippingFee, 0);

        try {
            $order = DB::transaction(function () use ($items, $subtotal, $discount, $shippingFee, $total, $coupon, $data) {
                foreach ($items as $line) {
                    $variant = $line['variant']->fresh();
                    if ($variant->stock_quantity < $line['quantity']) {
                        throw ValidationException::withMessages([
                            'cart' => "Sản phẩm \"{$variant->displayName()}\" không đủ tồn kho.",
                        ]);
                    }
                }

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => Order::STATUS_PENDING,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_fee' => $shippingFee,
                    'total' => $total,
                    'coupon_id' => $coupon?->id,
                    'ship_recipient' => $data['ship_recipient'],
                    'ship_phone' => $data['ship_phone'],
                    'ship_address' => $data['ship_address'],
                    'note' => $data['note'] ?? null,
                ]);

                foreach ($items as $line) {
                    $variant = $line['variant'];

                    $order->items()->create([
                        'variant_id' => $variant->id,
                        'product_name' => $variant->product->name,
                        'variant_name' => $variant->attribute,
                        'unit_price' => $variant->price,
                        'quantity' => $line['quantity'],
                        'line_total' => $line['line_total'],
                    ]);

                    $variant->decrement('stock_quantity', $line['quantity']);

                    InventoryMovement::create([
                        'variant_id' => $variant->id,
                        'type' => InventoryMovement::TYPE_EXPORT,
                        'quantity' => $line['quantity'],
                        'note' => "Đơn hàng {$order->code}",
                        'created_by' => Auth::id(),
                    ]);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'method' => $data['payment_method'],
                    'amount' => $total,
                    'status' => Payment::STATUS_PENDING,
                ]);

                Shipment::create([
                    'order_id' => $order->id,
                    'status' => 'preparing',
                ]);

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                return $order;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $this->cart->clear();
        session()->forget('checkout_coupon');

        // COD / bank transfer settle offline — go straight to the confirmation
        // page. Every other method has a `payments` row still pending, so
        // route through the gateway landing page (real redirect or demo
        // simulator, decided there) instead of guessing here.
        if (in_array($data['payment_method'], ['cod', 'bank_transfer'], true)) {
            return redirect()->route('checkout.success', $order)->with('success', 'Đặt hàng thành công!');
        }

        return redirect()->route('payment.gateway.show', $order);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'payments']);

        return view('checkout.success', compact('order'));
    }
}
