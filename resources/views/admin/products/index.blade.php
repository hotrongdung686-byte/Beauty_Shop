<x-admin-layout title="Sản phẩm">
    <div class="flex items-center justify-between mb-4 gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Sản phẩm</h1>
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm sản phẩm..." class="w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </form>
        <a href="{{ route('admin.products.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700 whitespace-nowrap">+ Thêm sản phẩm</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Sản phẩm</th>
                    <th class="px-4 py-3">Danh mục</th>
                    <th class="px-4 py-3">Giá</th>
                    <th class="px-4 py-3">Tồn kho</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ number_format($product->base_price) }}₫</td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->variants->sum('stock_quantity') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $product->is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Xóa sản phẩm này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $products->links() }}</div>
</x-admin-layout>
