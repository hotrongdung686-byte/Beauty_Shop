@props(['product'])

<div class="group">
    <a href="{{ route('products.show', $product) }}" class="block aspect-[3/4] bg-cream-100 relative overflow-hidden">
        @if($product->thumbnail)
            <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center text-clay-200 text-5xl font-karla font-bold">
                {{ Str::of($product->name)->substr(0, 1) }}
            </div>
        @endif
        @if($product->is_featured)
            <span class="absolute top-3 left-3 bg-white/90 text-ink text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-sm">Nổi bật</span>
        @endif
        @if($product->total_stock <= 0)
            <span class="absolute top-3 right-3 bg-ink/85 text-white text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-sm">Hết hàng</span>
        @endif
    </a>
    <div class="pt-3.5 text-center">
        @if($product->brand)
            <div class="text-[11px] uppercase tracking-widest text-ink/40 mb-1">{{ $product->brand->name }}</div>
        @endif
        <a href="{{ route('products.show', $product) }}" class="block font-karla font-semibold text-ink hover:text-clay-600 transition line-clamp-2">
            {{ $product->name }}
        </a>
        <div class="mt-1.5 text-sm text-ink/70">{{ number_format($product->display_price) }}₫</div>
    </div>
</div>
