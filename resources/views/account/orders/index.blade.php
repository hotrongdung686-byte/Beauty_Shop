<x-shop-layout title="Đơn hàng của tôi - {{ config('app.name') }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="font-karla font-bold text-3xl text-ink mb-8">Đơn hàng của tôi</h1>

        @if($orders->isEmpty())
            <div class="border border-cream-300 p-16 text-center text-ink/50">Bạn chưa có đơn hàng nào.</div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <a href="{{ route('account.orders.show', $order) }}" class="block border border-cream-300 p-5 hover:border-clay-600 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-karla font-semibold text-ink">#{{ $order->code }}</div>
                                <div class="text-xs text-ink/40 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items->count() }} sản phẩm</div>
                            </div>
                            <div class="text-right">
                                <div class="font-karla font-semibold text-ink">{{ number_format($order->total) }}₫</div>
                                <span class="inline-block mt-1 text-[11px] uppercase tracking-wider px-2.5 py-1 rounded-sm
                                    @class([
                                        'bg-amber-100 text-amber-700' => $order->status === 'pending',
                                        'bg-sky-100 text-sky-700' => in_array($order->status, ['confirmed','processing']),
                                        'bg-clay-100 text-clay-700' => $order->status === 'shipping',
                                        'bg-sage-100 text-sage-700' => $order->status === 'completed',
                                        'bg-red-100 text-red-700' => $order->status === 'cancelled',
                                    ])">{{ $order->status_label }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $orders->links() }}</div>
        @endif
    </div>
</x-shop-layout>
