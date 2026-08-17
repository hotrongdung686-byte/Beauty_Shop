<x-admin-layout title="{{ $banner->exists ? 'Sửa banner' : 'Thêm banner' }}">
    <a href="{{ route('admin.banners.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Banner</a>

    <form action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 max-w-2xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($banner->exists) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Tiêu đề</label>
                <input type="text" name="title" value="{{ old('title', $banner->title) }}" required placeholder="Sale hè rực rỡ" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Nhãn ưu đãi (badge)</label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="-30%" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600">Mô tả ngắn</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="Giảm giá lên đến 30% cho mỹ phẩm chăm sóc da" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Ảnh banner</label>
            @if($banner->image)
                <div class="mb-3 h-32 w-full max-w-sm rounded-lg overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/'.$banner->image) }}" class="w-full h-full object-cover">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="text-sm">
            <p class="text-xs text-gray-400 mt-1">Nếu để trống và có chọn sản phẩm liên kết, banner sẽ dùng ảnh đại diện của sản phẩm.</p>
            @if($banner->image)
                <label class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                    <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-400">
                    Xóa ảnh hiện tại
                </label>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Sản phẩm liên kết (tuỳ chọn)</label>
                <select name="product_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    <option value="">-- Không liên kết --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ (int) old('product_id', $banner->product_id) === $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Hoặc đường dẫn tuỳ chỉnh</label>
                <input type="text" name="custom_url" value="{{ old('custom_url', $banner->custom_url) }}" placeholder="/san-pham?category=cham-soc-da" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-gray-600">Chữ trên nút</label>
                <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text ?? 'Mua ngay') }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Thứ tự hiển thị</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Màu nền dự phòng</label>
                <input type="text" name="background_color" value="{{ old('background_color', $banner->background_color) }}" placeholder="#E5DDC8" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Bắt đầu hiển thị (tuỳ chọn)</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Kết thúc hiển thị (tuỳ chọn)</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->exists ? $banner->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
            Hiển thị trên trang chủ
        </label>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu banner</button>
    </form>
</x-admin-layout>
