<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->with('items')->latest()->paginate(10);

        return view('account.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load(['items', 'payments', 'shipment']);

        return view('account.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($order->canBeCancelled(), 400, 'Đơn hàng này không thể hủy.');

        $order->update(['status' => Order::STATUS_CANCELLED]);

        foreach ($order->items as $item) {
            $item->variant?->increment('stock_quantity', $item->quantity);
        }

        return back()->with('success', 'Đã hủy đơn hàng.');
    }
}
