<x-admin-layout title="Dịch vụ">
    <div class="flex items-center justify-between mb-4 gap-4">
        <h1 class="text-lg font-semibold text-gray-800">Dịch vụ</h1>
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Tìm dịch vụ..." class="w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
        </form>
        <a href="{{ route('admin.services.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700 whitespace-nowrap">+ Thêm dịch vụ</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Dịch vụ</th>
                    <th class="px-4 py-3">Nhóm</th>
                    <th class="px-4 py-3">Giá</th>
                    <th class="px-4 py-3">Thời lượng</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($services as $service)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                    @if($service->image)
                                        <img src="{{ asset('storage/'.$service->image) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                {{ $service->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $service->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ number_format($service->price) }}₫</td>
                        <td class="px-4 py-3 text-gray-500">{{ $service->duration_label }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $service->is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.services.edit', $service) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Xóa dịch vụ này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $services->links() }}</div>
</x-admin-layout>
