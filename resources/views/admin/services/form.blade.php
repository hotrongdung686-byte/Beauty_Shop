@php $assignedIds = $service->exists ? $service->staff->pluck('id')->all() : []; @endphp
<x-admin-layout title="{{ $service->exists ? 'Sửa dịch vụ' : 'Thêm dịch vụ' }}">
    <a href="{{ route('admin.services.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Dịch vụ</a>

    <form action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 max-w-3xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($service->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-gray-600">Tên dịch vụ</label>
            <input type="text" name="name" value="{{ old('name', $service->name) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Ảnh dịch vụ</label>
            @if($service->image)
                <div class="mb-3 h-32 w-32 rounded-lg overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/'.$service->image) }}" class="w-full h-full object-cover">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="text-sm">
            @if($service->image)
                <label class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                    <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-400">
                    Xóa ảnh hiện tại
                </label>
            @endif
        </div>

        <div>
            <label class="text-sm text-gray-600">Nhóm dịch vụ</label>
            <select name="service_category_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <option value="">-- Chọn nhóm --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (int) old('service_category_id', $service->service_category_id) === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm text-gray-600">Mô tả</label>
            <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-gray-600">Giá dịch vụ</label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $service->price) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Thời lượng (phút)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes ?? 60) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
            <div>
                <label class="text-sm text-gray-600">Tiền cọc</label>
                <input type="number" step="0.01" name="deposit_amount" value="{{ old('deposit_amount', $service->deposit_amount) }}" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            </div>
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Thợ thực hiện</label>
            <div class="flex flex-wrap gap-3">
                @forelse($allStaff as $staff)
                    <label class="flex items-center gap-2 text-sm bg-gray-50 rounded-lg px-3 py-1.5">
                        <input type="checkbox" name="staff_ids[]" value="{{ $staff->id }}" {{ in_array($staff->id, old('staff_ids', $assignedIds)) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                        {{ $staff->full_name }}
                    </label>
                @empty
                    <p class="text-sm text-gray-400">Chưa có nhân viên/thợ nào. <a href="{{ route('admin.staff.create') }}" class="text-rose-600 hover:underline">Thêm mới</a></p>
                @endforelse
            </div>
        </div>

        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->exists ? $service->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                Hiển thị
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $service->is_featured) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
                Nổi bật
            </label>
        </div>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu dịch vụ</button>
    </form>
</x-admin-layout>
