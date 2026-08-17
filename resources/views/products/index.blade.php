<x-shop-layout title="Sản phẩm - {{ config('app.name') }}">
    <div class="bg-cream-100 py-12 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="text-xs uppercase tracking-widest text-clay-600 mb-2">Cửa hàng</div>
            <h1 class="font-karla font-extrabold text-3xl sm:text-4xl text-ink">Tất cả sản phẩm</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <aside class="lg:col-span-1 space-y-8">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-8">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-ink/50">Tìm kiếm</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên sản phẩm..."
                               class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-widest text-ink/50 mb-3">Danh mục</div>
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="category" value="" {{ request('category') ? '' : 'checked' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                Tất cả
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-widest text-ink/50 mb-3">Thương hiệu</div>
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="brand" value="" {{ request('brand') ? '' : 'checked' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                Tất cả
                            </label>
                            @foreach($brands as $brand)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="brand" value="{{ $brand->slug }}" {{ request('brand') === $brand->slug ? 'checked' : '' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                    {{ $brand->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button class="w-full border border-ink text-ink text-xs uppercase tracking-wider py-2.5 rounded-sm hover:bg-ink hover:text-white transition">Lọc</button>
                </form>
            </aside>

            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-cream-300">
                    <div class="text-sm text-ink/50">{{ $products->total() }} sản phẩm</div>
                    <form method="GET" action="{{ route('products.index') }}" class="flex items-center gap-2">
                        @foreach(request()->except('sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label class="text-xs uppercase tracking-widest text-ink/50">Sắp xếp</label>
                        <select name="sort" onchange="this.form.submit()" class="text-sm border-ink/20 rounded-sm focus:border-ink focus:ring-ink">
                            <option value="" {{ request('sort') ? '' : 'selected' }}>Nổi bật</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </form>
                </div>

                @if($products->isEmpty())
                    <div class="border border-cream-300 p-14 text-center text-ink/50">Không tìm thấy sản phẩm phù hợp.</div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-5 gap-y-10">
                        @foreach($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="mt-12">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-shop-layout>
