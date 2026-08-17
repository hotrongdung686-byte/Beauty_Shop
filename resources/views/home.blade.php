<x-shop-layout>
    {{-- Hero / promo banner carousel --}}
    @if($banners->isNotEmpty())
        <x-banner-carousel :banners="$banners" />
    @else
        <section class="relative bg-gradient-to-br from-cream-100 via-cream-200 to-sage-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[70svh] flex items-end pb-14 pt-24">
                <div class="max-w-2xl">
                    <h1 class="font-karla font-extrabold text-4xl sm:text-6xl lg:text-7xl leading-[0.98] tracking-tight text-ink">
                        Vẻ đẹp hoài cổ<br>gặp gỡ nét tươi mới
                    </h1>
                    <p class="mt-6 text-base sm:text-lg text-ink/70 max-w-lg">Mỹ phẩm chính hãng &amp; các nghi thức làm đẹp — gội đầu, phun xăm, nối mi, chăm sóc da — được chọn lọc cho làn da và mái tóc của bạn.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center bg-ink text-white text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink/85 transition">Mua mỹ phẩm</a>
                        <a href="{{ route('services.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink hover:text-white transition">Đặt lịch dịch vụ</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Category strip --}}
    @if($categories->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-end justify-between mb-8">
            <div>
                <div class="text-xs uppercase tracking-widest text-clay-600 mb-2">Khám phá</div>
                <h2 class="font-karla font-bold text-2xl sm:text-3xl text-ink">Danh mục mỹ phẩm</h2>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
            @foreach($categories as $cat)
                <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="group block">
                    <div class="aspect-square bg-cream-100 flex items-center justify-center relative overflow-hidden">
                        @if($cat->image)
                            <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                            <span class="font-karla font-bold text-3xl text-clay-600/60 group-hover:scale-110 transition duration-500">{{ Str::of($cat->name)->substr(0, 1) }}</span>
                        @endif
                    </div>
                    <div class="pt-3 text-center">
                        <div class="font-karla font-semibold text-sm text-ink">{{ $cat->name }}</div>
                        <div class="text-xs text-ink/40">{{ $cat->products_count }} sản phẩm</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Featured products --}}
    @if($featuredProducts->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-20">
        <div class="flex items-end justify-between mb-8">
            <div>
                <div class="text-xs uppercase tracking-widest text-clay-600 mb-2">Được yêu thích</div>
                <h2 class="font-karla font-bold text-2xl sm:text-3xl text-ink">Sản phẩm nổi bật</h2>
            </div>
            <a href="{{ route('products.index') }}" class="hidden sm:inline text-sm text-ink/60 hover:text-clay-600 border-b border-ink/30 hover:border-clay-600 transition">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-10">
            @foreach($featuredProducts->take(4) as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </section>
    @endif

    {{-- Split showcase --}}
    <section class="bg-ink">
        <div class="grid md:grid-cols-2">
            <div class="bg-cream-200 flex flex-col items-center justify-center text-center gap-5 py-20 px-8">
                <h3 class="font-karla font-bold text-2xl sm:text-3xl text-ink max-w-xs">Làm đẹp được tái định nghĩa cho hôm nay</h3>
                <a href="{{ route('products.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-6 py-3 rounded-sm hover:bg-ink hover:text-white transition">Mua ngay</a>
            </div>
            <div class="bg-clay-200/60 flex flex-col items-center justify-center text-center gap-5 py-20 px-8">
                <h3 class="font-karla font-bold text-2xl sm:text-3xl text-ink max-w-xs">Nghi thức chăm sóc dành riêng cho bạn</h3>
                <a href="{{ route('services.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-6 py-3 rounded-sm hover:bg-ink hover:text-white transition">Đặt lịch</a>
            </div>
        </div>
    </section>

    {{-- Marquee --}}
    <div class="bg-cream-100 py-4 overflow-hidden border-y border-cream-300">
        <div class="flex items-center gap-8 whitespace-nowrap animate-[marquee_28s_linear_infinite] text-ink/50 font-karla font-semibold text-lg uppercase tracking-wide">
            @for($i = 0; $i < 3; $i++)
                <span>Chăm sóc da</span><span class="text-clay-600">✦</span>
                <span>Phun xăm thẩm mỹ</span><span class="text-clay-600">✦</span>
                <span>Nối mi</span><span class="text-clay-600">✦</span>
                <span>Gội đầu dưỡng sinh</span><span class="text-clay-600">✦</span>
            @endfor
        </div>
    </div>

    {{-- Featured services editorial --}}
    @if($featuredServices->isNotEmpty())
    <section class="bg-sage-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="aspect-[4/5] bg-sage-100 flex items-center justify-center order-2 md:order-1 overflow-hidden">
                    @if($featuredServices->first()->image)
                        <img src="{{ asset('storage/'.$featuredServices->first()->image) }}" alt="{{ $featuredServices->first()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="font-karla font-bold text-7xl text-sage-600/40">{{ Str::of($featuredServices->first()->name)->substr(0,1) }}</span>
                    @endif
                </div>
                <div class="order-1 md:order-2">
                    <div class="text-xs uppercase tracking-widest text-sage-700 mb-3">Được đặt nhiều nhất</div>
                    <h3 class="font-karla font-bold text-3xl sm:text-4xl text-ink mb-4">{{ $featuredServices->first()->name }}</h3>
                    <p class="text-ink/70 max-w-md mb-6">{{ $featuredServices->first()->description ?? 'Dịch vụ làm đẹp chuyên nghiệp, tận tâm với từng khách hàng.' }}</p>
                    <div class="text-2xl font-karla font-semibold text-ink mb-6">{{ number_format($featuredServices->first()->price) }}₫</div>
                    <a href="{{ route('booking.create', $featuredServices->first()) }}" class="inline-flex items-center bg-ink text-white text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink/85 transition">Đặt lịch ngay</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- Pull quote --}}
    <section class="bg-cream-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <div class="font-karla font-bold text-2xl sm:text-3xl text-ink leading-relaxed">
                &ldquo;Chúng tôi tin rằng vẻ đẹp nên vừa hoài niệm, vừa mới mẻ — mời bạn tận hưởng những nghi thức tôn vinh cả di sản lẫn sự tinh tế hiện đại.&rdquo;
            </div>
            <a href="{{ route('services.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink hover:text-white transition mt-8">Khám phá dịch vụ</a>
        </div>
    </section>

    {{-- Featured services grid --}}
    @if($serviceCategories->count())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex items-end justify-between mb-8">
            <div>
                <div class="text-xs uppercase tracking-widest text-clay-600 mb-2">Chăm sóc trọn vẹn</div>
                <h2 class="font-karla font-bold text-2xl sm:text-3xl text-ink">Dịch vụ nổi bật</h2>
            </div>
            <a href="{{ route('services.index') }}" class="hidden sm:inline text-sm text-ink/60 hover:text-clay-600 border-b border-ink/30 hover:border-clay-600 transition">Xem tất cả</a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-5 gap-y-10">
            @foreach($featuredServices->take(4) as $service)
                <x-service-card :service="$service" />
            @endforeach
        </div>
    </section>
    @endif
</x-shop-layout>
