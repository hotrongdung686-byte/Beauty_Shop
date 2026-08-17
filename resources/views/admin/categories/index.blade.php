<x-admin-layout title="Danh mục sản phẩm">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Danh mục sản phẩm</h1>
        <a href="{{ route('admin.categories.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">+ Thêm danh mục</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Tên</th>
                    <th class="px-4 py-3">Danh mục cha</th>
                    <th class="px-4 py-3">Số sản phẩm</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                    @if($category->image)
                                        <img src="{{ asset('storage/'.$category->image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                {{ $category->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $category->parent?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $category->is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Xóa danh mục này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</x-admin-layout>
