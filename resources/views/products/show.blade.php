<x-shop-layout title="{{ $product->name }} - {{ config('app.name') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
        variantId: {{ $product->variants->first()?->id ?? 'null' }},
        variants: {{ $product->variants->map(fn($v) => ['id' => $v->id, 'price' => (float) $v->price, 'attribute' => $v->attribute, 'stock' => $v->stock_quantity])->toJson() }},
        get variant() { return this.variants.find(v => v.id === this.variantId) },
    }">
        <nav class="text-xs uppercase tracking-widest text-ink/40 mb-8">
            <a href="{{ route('home') }}" class="hover:text-clay-600">Trang chủ</a> /
            <a href="{{ route('products.index') }}" class="hover:text-clay-600">Sản phẩm</a> /
            <span class="text-ink/70">{{ $product->name }}</span>
        </nav>

        <div class="grid md:grid-cols-2 gap-12 lg:gap-16">
            <div>
                <div class="aspect-[3/4] bg-cream-100 overflow-hidden flex items-center justify-center">
                    @if($product->thumbnail)
                        <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-clay-200 text-7xl font-karla font-bold">{{ Str::of($product->name)->substr(0, 1) }}</span>
                    @endif
                </div>
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-5 gap-2 mt-3">
                        @foreach($product->images as $img)
                            <div class="aspect-square bg-cream-100 overflow-hidden">
                                <img src="{{ asset('storage/'.$img->path) }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="max-w-lg">
                @if($product->brand)
                    <div class="text-xs uppercase tracking-widest text-clay-600">{{ $product->brand->name }}</div>
                @endif
                <h1 class="font-karla font-bold text-3xl sm:text-4xl text-ink mt-2">{{ $product->name }}</h1>

                <div class="flex items-center gap-1.5 mt-3 text-sm text-clay-600">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{{ $i <= round($product->average_rating) ? '★' : '☆' }}</span>
                    @endfor
                    <span class="text-ink/40 ml-1">({{ $product->average_rating ?: '0' }}/5 · {{ $reviews->total() }} đánh giá)</span>
                </div>

                <div class="mt-5 text-2xl font-karla font-semibold text-ink" x-text="new Intl.NumberFormat('vi-VN').format(variant ? variant.price : {{ $product->base_price }}) + '₫'"></div>

                @if($product->short_desc)
                    <p class="mt-5 text-ink/70 leading-relaxed">{{ $product->short_desc }}</p>
                @endif

                @if($product->variants->count() > 1)
                    <div class="mt-7">
                        <div class="text-xs uppercase tracking-widest text-ink/50 mb-3">Phân loại</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->variants as $variant)
                                <button type="button" @click="variantId = {{ $variant->id }}"
                                        :class="variantId === {{ $variant->id }} ? 'border-ink bg-ink text-white' : 'border-ink/25 text-ink/70'"
                                        class="px-4 py-2 border rounded-sm text-sm transition">
                                    {{ $variant->attribute ?? 'Mặc định' }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-3 text-sm" x-show="variant">
                    <span x-show="variant && variant.stock > 0" class="text-sage-700">Còn hàng (<span x-text="variant?.stock"></span>)</span>
                    <span x-show="variant && variant.stock <= 0" class="text-red-500">Hết hàng</span>
                </div>

                <form action="{{ route('cart.add') }}" method="POST" class="mt-7 flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="variant_id" :value="variantId">
                    <input type="number" name="quantity" value="1" min="1" max="99" class="w-20 border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                    <button type="submit" :disabled="variant && variant.stock <= 0"
                            class="flex-1 bg-ink text-white text-sm uppercase tracking-wider py-3.5 rounded-sm hover:bg-ink/85 transition disabled:opacity-40 disabled:cursor-not-allowed">
                        Thêm vào giỏ hàng
                    </button>
                    @auth
                        <button type="button" onclick="document.getElementById('wishlist-form').submit()" class="p-3.5 border border-ink/20 rounded-sm hover:border-clay-600 hover:text-clay-600 transition">♥</button>
                    @endauth
                </form>
                @auth
                    <form id="wishlist-form" action="{{ route('wishlist.toggle', $product) }}" method="POST" class="hidden">@csrf</form>
                @endauth

                @if($product->description)
                    <div class="mt-10 border-t border-cream-300 pt-8">
                        <h2 class="font-karla font-semibold text-ink mb-3">Mô tả sản phẩm</h2>
                        <div class="text-ink/70 leading-relaxed whitespace-pre-line">{{ $product->description }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reviews --}}
        <div class="mt-16 border-t border-cream-300 pt-10 max-w-3xl">
            <h2 class="font-karla font-bold text-xl text-ink mb-6">Đánh giá sản phẩm</h2>

            @auth
                <form action="{{ route('reviews.product.store', $product) }}" method="POST" class="border border-cream-300 p-5 mb-8">
                    @csrf
                    <div class="text-sm font-semibold text-ink mb-2">Đánh giá của bạn</div>
                    <div class="flex gap-1 mb-3" x-data="{ rating: 5 }">
                        <input type="hidden" name="rating" x-model="rating">
                        <template x-for="i in 5">
                            <button type="button" @click="rating = i" class="text-2xl" :class="i <= rating ? 'text-clay-600' : 'text-ink/20'">★</button>
                        </template>
                    </div>
                    <textarea name="comment" rows="3" placeholder="Chia sẻ cảm nhận của bạn..." class="w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink"></textarea>
                    <button class="mt-3 bg-ink text-white px-5 py-2.5 rounded-sm text-sm uppercase tracking-wider hover:bg-ink/85 transition">Gửi đánh giá</button>
                </form>
            @endauth

            <div class="space-y-5">
                @forelse($reviews as $review)
                    <div class="border-b border-cream-200 pb-5">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-ink">{{ $review->user->name }}</div>
                            <div class="text-clay-600 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                        </div>
                        <div class="text-xs text-ink/40">{{ $review->created_at->format('d/m/Y') }}</div>
                        @if($review->comment)
                            <p class="mt-2 text-ink/70 text-sm">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-ink/40 text-sm">Chưa có đánh giá nào.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $reviews->links() }}</div>
        </div>

        @if($related->count())
        <div class="mt-16 border-t border-cream-300 pt-10">
            <h2 class="font-karla font-bold text-xl text-ink mb-6">Sản phẩm liên quan</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-5 gap-y-10">
                @foreach($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-shop-layout>
