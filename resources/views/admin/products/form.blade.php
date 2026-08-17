<x-admin-layout title="{{ $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Sản phẩm</a>

    <form action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" method="POST" class="mt-4 max-w-3xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-gray-600">Tên sản phẩm</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Danh mục</label>
                <select name="category_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (int) old('category_id', $product->category_id) === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Thương hiệu</label>
                <select name="brand_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    <option value="">-- Chọn thương hiệu --</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ (int) old('brand_id', $product->brand_id) === $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600">Mô tả ngắn</label>
            <input type="text" name="short_desc" value="{{ old('short_desc', $product->short_desc) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div>
            <label class="text-sm text-gray-600">Mô tả chi tiết</label>
            <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            <label class="text-sm text-gray-600">Giá niêm yết</label>
            <input type="number" step="0.01" name="base_price" value="{{ old('base_price', $product->base_price) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->exists ? $product->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                Hiển thị
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                Nổi bật
            </label>
        </div>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu sản phẩm</button>
    </form>

    @if($product->exists)
        {{-- Variants --}}
        <div class="mt-8 max-w-3xl bg-white border border-gray-100 rounded-xl p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Phân loại (SKU / tồn kho)</h2>

            <div class="divide-y divide-gray-100 mb-4">
                @foreach($product->variants as $variant)
                    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST" class="flex items-center gap-3 py-3">
                        @csrf @method('PATCH')
                        <div class="text-xs text-gray-400 font-mono w-28 shrink-0">{{ $variant->sku }}</div>
                        <input type="text" name="attribute" value="{{ $variant->attribute }}" placeholder="Phân loại" class="flex-1 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <input type="number" step="0.01" name="price" value="{{ $variant->price }}" class="w-32 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <input type="number" name="stock_quantity" value="{{ $variant->stock_quantity }}" class="w-24 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <button class="text-rose-600 text-sm hover:underline">Lưu</button>
                        <button type="submit" formaction="{{ route('admin.products.variants.destroy', [$product, $variant]) }}" formmethod="POST"
                                onclick="return confirm('Xóa phân loại này?')"
                                class="text-gray-400 hover:text-red-500 text-sm">Xóa</button>
                    </form>
                @endforeach
            </div>

            <form action="{{ route('admin.products.variants.store', $product) }}" method="POST" class="flex items-center gap-3 pt-3 border-t border-gray-100">
                @csrf
                <input type="text" name="sku" placeholder="SKU" required class="w-28 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <input type="text" name="attribute" placeholder="Phân loại (vd: 50ml)" class="flex-1 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <input type="number" step="0.01" name="price" placeholder="Giá" required class="w-32 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <input type="number" name="stock_quantity" placeholder="Tồn kho" value="0" required class="w-24 rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <button class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900">+ Thêm</button>
            </form>
        </div>

        {{-- Images --}}
        <div class="mt-8 max-w-3xl bg-white border border-gray-100 rounded-xl p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Hình ảnh</h2>

            <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-4">
                @foreach($product->images as $image)
                    <div class="relative group">
                        <div class="aspect-square rounded-lg overflow-hidden border {{ $image->is_primary ? 'border-rose-500' : 'border-gray-100' }}">
                            <img src="{{ asset('storage/'.$image->path) }}" class="w-full h-full object-cover">
                        </div>
                        <div class="mt-1 flex justify-between text-xs">
                            @if(!$image->is_primary)
                                <form action="{{ route('admin.products.images.primary', [$product, $image]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="text-gray-400 hover:text-rose-600">Đặt chính</button>
                                </form>
                            @else
                                <span class="text-rose-600">Ảnh chính</span>
                            @endif
                            <form action="{{ route('admin.products.images.destroy', [$product, $image]) }}" method="POST" onsubmit="return confirm('Xóa ảnh này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('admin.products.images.store', $product) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="image" accept="image/*" required class="text-sm">
                <button class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-900">Tải ảnh lên</button>
            </form>
        </div>
    @endif
</x-admin-layout>
