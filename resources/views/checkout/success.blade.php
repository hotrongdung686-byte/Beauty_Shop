<x-shop-layout title="Đặt hàng thành công - {{ config('app.name') }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <div class="h-16 w-16 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center mx-auto text-3xl">✓</div>
        <h1 class="font-karla font-bold text-3xl text-ink mt-6">Đặt hàng thành công!</h1>
        <p class="text-ink/50 mt-2">Mã đơn hàng của bạn: <span class="font-semibold text-ink">{{ $order->code }}</span></p>

        @php $latestPayment = $order->payments->sortByDesc('id')->first(); @endphp
        @if($latestPayment && $latestPayment->method !== 'cod')
            <div class="mt-6 inline-flex items-center gap-2 text-sm px-4 py-2 rounded-sm
                @class([
                    'bg-sage-100 text-sage-700' => $latestPayment->status === 'paid',
                    'bg-amber-100 text-amber-700' => $latestPayment->status === 'pending',
                    'bg-red-100 text-red-700' => in_array($latestPayment->status, ['failed', 'refunded']),
                ])">
                Thanh toán {{ strtoupper($latestPayment->method) }}:
                {{ ['paid' => 'Đã thanh toán', 'pending' => 'Đang chờ xử lý', 'failed' => 'Thất bại', 'refunded' => 'Đã hoàn tiền'][$latestPayment->status] ?? $latestPayment->status }}
                @if(in_array($latestPayment->status, ['pending', 'failed']))
                    <a href="{{ route('payment.gateway.show', $order) }}" class="underline">Thanh toán lại</a>
                @endif
            </div>
        @endif

        <div class="border border-cream-300 p-6 mt-10 text-left">
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Người nhận</span><span class="font-medium text-ink">{{ $order->ship_recipient }}</span></div>
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Điện thoại</span><span class="font-medium text-ink">{{ $order->ship_phone }}</span></div>
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Địa chỉ</span><span class="font-medium text-ink text-right">{{ $order->ship_address }}</span></div>
            <div class="border-t border-cream-200 my-3"></div>
            @foreach($order->items as $item)
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-ink/60">{{ $item->product_name }}{{ $item->variant_name ? ' - '.$item->variant_name : '' }} x{{ $item->quantity }}</span>
                    <span class="text-ink">{{ number_format($item->line_total) }}₫</span>
                </div>
            @endforeach
            <div class="border-t border-cream-200 my-3"></div>
            <div class="flex justify-between font-karla font-semibold text-ink"><span>Tổng cộng</span><span>{{ number_format($order->total) }}₫</span></div>
        </div>

        <div class="mt-10 flex justify-center gap-3">
            <a href="{{ route('account.orders.show', $order) }}" class="inline-flex items-center bg-ink text-white text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink/85 transition">Xem đơn hàng</a>
            <a href="{{ route('products.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink hover:text-white transition">Tiếp tục mua sắm</a>
        </div>
    </div>
</x-shop-layout>
