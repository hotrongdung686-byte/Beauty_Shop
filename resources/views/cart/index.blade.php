<x-shop-layout title="Giỏ hàng - {{ config('app.name') }}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="font-karla font-bold text-3xl text-ink mb-8">Giỏ hàng của bạn</h1>

        @if($items->isEmpty())
            <div class="border border-cream-300 p-16 text-center">
                <p class="text-ink/50">Giỏ hàng của bạn đang trống.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center mt-6 bg-ink text-white text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink/85 transition">Tiếp tục mua sắm</a>
            </div>
        @else
            <div class="border border-cream-300 divide-y divide-cream-200">
                @foreach($items as $line)
                    @php $variant = $line['variant']; @endphp
                    <div class="flex items-center gap-4 p-4">
                        <div class="h-16 w-16 bg-cream-100 shrink-0 flex items-center justify-center text-clay-200 font-karla font-bold overflow-hidden">
                            @if($variant->product->thumbnail)
                                <img src="{{ asset('storage/'.$variant->product->thumbnail) }}" class="w-full h-full object-cover">
                            @else
                                {{ Str::of($variant->product->name)->substr(0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $variant->product) }}" class="font-karla font-semibold text-ink hover:text-clay-600 transition line-clamp-1">{{ $variant->product->name }}</a>
                            @if($variant->attribute)
                                <div class="text-xs text-ink/40">{{ $variant->attribute }}</div>
                            @endif
                            <div class="text-ink/70 text-sm mt-1">{{ number_format($variant->price) }}₫</div>
                        </div>
                        <form action="{{ route('cart.update', $variant) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="0" max="99"
                                   class="w-16 border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink"
                                   onchange="this.form.submit()">
                        </form>
                        <div class="w-28 text-right font-karla font-semibold text-ink">{{ number_format($line['line_total']) }}₫</div>
                        <form action="{{ route('cart.remove', $variant) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="text-ink/30 hover:text-red-500 text-sm transition">Xóa</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <div class="w-full sm:w-80 border border-cream-300 p-6">
                    <div class="flex justify-between text-ink/60 text-sm mb-2">
                        <span>Tạm tính</span>
                        <span class="font-semibold text-ink">{{ number_format($subtotal) }}₫</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="block mt-5 bg-ink text-white text-center text-sm uppercase tracking-wider py-3.5 rounded-sm hover:bg-ink/85 transition">
                        Tiến hành thanh toán
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>
