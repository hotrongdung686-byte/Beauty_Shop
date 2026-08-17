<x-admin-layout title="Thương hiệu">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Thương hiệu</h1>
        <a href="{{ route('admin.brands.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">+ Thêm thương hiệu</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Tên</th>
                    <th class="px-4 py-3">Số sản phẩm</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($brands as $brand)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-lg bg-gray-50 overflow-hidden shrink-0 flex items-center justify-center">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/'.$brand->logo) }}" class="w-full h-full object-contain">
                                    @endif
                                </div>
                                {{ $brand->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $brand->products_count }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.brands.edit', $brand) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" class="inline" onsubmit="return confirm('Xóa thương hiệu này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $brands->links() }}</div>
</x-admin-layout>
