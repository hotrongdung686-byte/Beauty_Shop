<x-admin-layout title="{{ $brand->exists ? 'Sửa thương hiệu' : 'Thêm thương hiệu' }}">
    <a href="{{ route('admin.brands.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Thương hiệu</a>

    <form action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 max-w-xl bg-white border border-gray-100 rounded-xl p-6 space-y-4">
        @csrf
        @if($brand->exists) @method('PUT') @endif

        <div>
            <label class="text-sm text-gray-600">Tên thương hiệu</label>
            <input type="text" name="name" value="{{ old('name', $brand->name) }}" required class="mt-1 w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </div>

        <div>
            <label class="text-sm text-gray-600 mb-2 block">Logo</label>
            @if($brand->logo)
                <div class="mb-3 h-20 w-20 rounded-lg overflow-hidden border border-gray-100 bg-gray-50 flex items-center justify-center">
                    <img src="{{ asset('storage/'.$brand->logo) }}" class="w-full h-full object-contain">
                </div>
            @endif
            <input type="file" name="logo" accept="image/*" class="text-sm">
            @if($brand->logo)
                <label class="flex items-center gap-2 text-sm text-gray-600 mt-2">
                    <input type="checkbox" name="remove_logo" value="1" class="rounded text-rose-600 focus:ring-rose-400">
                    Xóa logo hiện tại
                </label>
            @endif
        </div>

        <button class="bg-rose-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Lưu thương hiệu</button>
    </form>
</x-admin-layout>
