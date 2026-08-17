<x-admin-layout title="{{ $category->exists ? 'Sửa danh mục' : 'Thêm danh mục' }}">
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Danh mục sản phẩm</a>

    <form action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 max-w-xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-gray-600">Tên danh mục</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Ảnh danh mục</label>
            @if($category->image)
                <div class="mb-3 h-24 w-24 rounded-lg overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/'.$category->image) }}" class="w-full h-full object-cover">
                </div>
            @endif
            <input type="file" name="image" accept="image/*" class="text-sm">
            @if($category->image)
                <label class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                    <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600 focus:ring-rose-400">
                    Xóa ảnh hiện tại
                </label>
            @endif
        </div>

        <div>
            <label class="text-sm text-gray-600">Danh mục cha (tuỳ chọn)</label>
            <select name="parent_id" class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <option value="">-- Không có --</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ (int) old('parent_id', $category->parent_id) === $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->exists ? $category->is_active : true) ? 'checked' : '' }} class="rounded text-rose-600 focus:ring-rose-400">
            Hiển thị trên website
        </label>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu danh mục</button>
    </form>
</x-admin-layout>
