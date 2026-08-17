<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($status = $request->string('status')->value()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('code', 'like', "%{$search}%");
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'payments', 'shipment', 'user', 'coupon']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipping,completed,cancelled'],
        ]);

        $order->update(['status' => $data['status']]);

        if ($data['status'] === Order::STATUS_COMPLETED) {
            $order->payments()->where('status', Payment::STATUS_PENDING)->update([
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
            ]);
        }

        if ($data['status'] === Order::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                $item->variant?->increment('stock_quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Đã cập nhật trạng thái đơn hàng.');
    }

    public function updateShipment(Request $request, Order $order)
    {
        $data = $request->validate([
            'carrier' => ['nullable', 'string', 'max:100'],
            'tracking_code' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:preparing,picked,in_transit,delivered,returned'],
        ]);

        $order->shipment()->updateOrCreate(['order_id' => $order->id], $data + [
            'shipped_at' => in_array($data['status'], ['picked', 'in_transit', 'delivered']) ? ($order->shipment?->shipped_at ?? now()) : null,
            'delivered_at' => $data['status'] === 'delivered' ? now() : null,
        ]);

        return back()->with('success', 'Đã cập nhật vận chuyển.');
    }
}
