<x-admin-layout title="Đơn hàng #{{ $order->code }}">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Đơn hàng</a>

    <div class="mt-4 grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Đơn hàng #{{ $order->code }}</h2>
                <div class="text-sm text-gray-500 space-y-1">
                    <div>Khách hàng: <span class="text-gray-800 font-medium">{{ $order->user?->name ?? 'Khách vãng lai' }}</span></div>
                    <div>Người nhận: {{ $order->ship_recipient }} - {{ $order->ship_phone }}</div>
                    <div>Địa chỉ: {{ $order->ship_address }}</div>
                    @if($order->note)<div>Ghi chú: {{ $order->note }}</div>@endif
                    <div>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                    @if($order->coupon)<div>Mã giảm giá: {{ $order->coupon->code }}</div>@endif
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="flex justify-between p-4 text-sm">
                        <div>
                            <div class="font-medium text-gray-800">{{ $item->product_name }}</div>
                            @if($item->variant_name)<div class="text-gray-400 text-xs">{{ $item->variant_name }}</div>@endif
                            <div class="text-gray-400 text-xs">{{ number_format($item->unit_price) }}₫ x {{ $item->quantity }}</div>
                        </div>
                        <div class="font-semibold text-gray-800">{{ number_format($item->line_total) }}₫</div>
                    </div>
                @endforeach
                <div class="p-4 text-sm space-y-1">
                    <div class="flex justify-between text-gray-500"><span>Tạm tính</span><span>{{ number_format($order->subtotal) }}₫</span></div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-emerald-600"><span>Giảm giá</span><span>-{{ number_format($order->discount_amount) }}₫</span></div>
                    @endif
                    <div class="flex justify-between text-gray-500"><span>Phí vận chuyển</span><span>{{ number_format($order->shipping_fee) }}₫</span></div>
                    <div class="flex justify-between font-bold text-gray-900 pt-1 border-t border-gray-100"><span>Tổng cộng</span><span class="text-rose-600">{{ number_format($order->total) }}₫</span></div>
                </div>
            </div>

            @if($order->payments->count())
                <div class="bg-white border border-gray-100 rounded-xl p-5">
                    <h2 class="font-semibold text-gray-800 mb-3">Thanh toán</h2>
                    @foreach($order->payments as $payment)
                        <div class="flex justify-between text-sm py-1">
                            <span class="text-gray-500">{{ strtoupper($payment->method) }}</span>
                            <span class="font-medium">{{ number_format($payment->amount) }}₫ · {{ $payment->status }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Trạng thái đơn hàng</h2>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        @foreach(\App\Models\Order::statusLabels() as $value => $label)
                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-rose-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Cập nhật trạng thái</button>
                </form>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Vận chuyển</h2>
                <form action="{{ route('admin.orders.shipment', $order) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <div>
                        <label class="text-xs text-gray-500">Đơn vị vận chuyển</label>
                        <input type="text" name="carrier" value="{{ $order->shipment?->carrier }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Mã vận đơn</label>
                        <input type="text" name="tracking_code" value="{{ $order->shipment?->tracking_code }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500">Trạng thái</label>
                        <select name="status" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                            @foreach(['preparing' => 'Đang chuẩn bị', 'picked' => 'Đã lấy hàng', 'in_transit' => 'Đang vận chuyển', 'delivered' => 'Đã giao', 'returned' => 'Hoàn trả'] as $value => $label)
                                <option value="{{ $value }}" {{ $order->shipment?->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="w-full bg-gray-800 text-white py-2 rounded-lg text-sm font-medium hover:bg-gray-900">Cập nhật vận chuyển</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
