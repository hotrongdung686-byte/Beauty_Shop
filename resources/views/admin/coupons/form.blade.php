<x-admin-layout title="{{ $coupon->exists ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá' }}">
    <a href="{{ route('admin.coupons.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Mã giảm giá</a>

    <form action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="POST" class="mt-4 max-w-xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($coupon->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-gray-600">Mã</label>
            <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm uppercase focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Loại giảm giá</label>
                <select name="type" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                    <option value="percent" {{ old('type', $coupon->type) === 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Số tiền cố định</option>
                </select>
            </div>
            <div>
                <label class="text-sm text-gray-600">Giá trị</label>
                <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Đơn tối thiểu</label>
                <input type="number" step="0.01" name="min_order" value="{{ old('min_order', $coupon->min_order) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Giảm tối đa (nếu %)</label>
                <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600">Giới hạn lượt dùng (để trống = không giới hạn)</label>
            <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-gray-600">Bắt đầu</label>
                <input type="date" name="starts_at" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Kết thúc</label>
                <input type="date" name="ends_at" value="{{ old('ends_at', $coupon->ends_at?->format('Y-m-d')) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->exists ? $coupon->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
            Kích hoạt
        </label>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu mã giảm giá</button>
    </form>
</x-admin-layout>
