<x-shop-layout title="Đơn hàng #{{ $order->code }} - {{ config('app.name') }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <a href="{{ route('account.orders.index') }}" class="text-xs uppercase tracking-widest text-ink/40 hover:text-clay-600 transition">&larr; Đơn hàng của tôi</a>

        <div class="flex items-center justify-between mt-4 mb-8">
            <h1 class="font-karla font-bold text-3xl text-ink">Đơn hàng #{{ $order->code }}</h1>
            <span class="text-[11px] uppercase tracking-wider px-3 py-1.5 rounded-sm
                @class([
                    'bg-amber-100 text-amber-700' => $order->status === 'pending',
                    'bg-sky-100 text-sky-700' => in_array($order->status, ['confirmed','processing']),
                    'bg-clay-100 text-clay-700' => $order->status === 'shipping',
                    'bg-sage-100 text-sage-700' => $order->status === 'completed',
                    'bg-red-100 text-red-700' => $order->status === 'cancelled',
                ])">{{ $order->status_label }}</span>
        </div>

        <div class="border border-cream-300 p-6 mb-6">
            <h2 class="font-karla font-semibold text-ink mb-3">Thông tin giao hàng</h2>
            <div class="text-sm text-ink/60 space-y-1">
                <div>{{ $order->ship_recipient }} · {{ $order->ship_phone }}</div>
                <div>{{ $order->ship_address }}</div>
                @if($order->note)<div class="text-ink/40">Ghi chú: {{ $order->note }}</div>@endif
            </div>
            @if($order->shipment)
                <div class="mt-3 text-sm text-ink/50">Vận chuyển: {{ $order->shipment->status }} @if($order->shipment->tracking_code) · Mã vận đơn: {{ $order->shipment->tracking_code }} @endif</div>
            @endif
        </div>

        <div class="border border-cream-300 divide-y divide-cream-200 mb-6">
            @foreach($order->items as $item)
                <div class="flex justify-between p-4 text-sm">
                    <div>
                        <div class="font-medium text-ink">{{ $item->product_name }}</div>
                        @if($item->variant_name)<div class="text-ink/40 text-xs">{{ $item->variant_name }}</div>@endif
                        <div class="text-ink/40 text-xs">{{ number_format($item->unit_price) }}₫ x {{ $item->quantity }}</div>
                    </div>
                    <div class="font-semibold text-ink">{{ number_format($item->line_total) }}₫</div>
                </div>
            @endforeach
        </div>

        <div class="border border-cream-300 p-6">
            <div class="flex justify-between text-sm text-ink/60 mb-1"><span>Tạm tính</span><span>{{ number_format($order->subtotal) }}₫</span></div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-sm text-sage-700 mb-1"><span>Giảm giá</span><span>-{{ number_format($order->discount_amount) }}₫</span></div>
            @endif
            <div class="flex justify-between text-sm text-ink/60 mb-1"><span>Phí vận chuyển</span><span>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee).'₫' : 'Miễn phí' }}</span></div>
            <div class="flex justify-between font-karla font-semibold text-ink border-t border-cream-200 pt-3 mt-3"><span>Tổng cộng</span><span>{{ number_format($order->total) }}₫</span></div>
        </div>

        @if($order->canBeCancelled())
            <form action="{{ route('account.orders.cancel', $order) }}" method="POST" class="mt-8" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                @csrf
                <button class="border border-red-300 text-red-600 px-6 py-3 rounded-sm text-sm uppercase tracking-wider hover:bg-red-50 transition">Hủy đơn hàng</button>
            </form>
        @endif
    </div>
</x-shop-layout>
