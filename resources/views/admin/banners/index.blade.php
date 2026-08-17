<x-admin-layout title="Banner">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Banner khuyến mãi</h1>
        <a href="{{ route('admin.banners.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">+ Thêm banner</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Banner</th>
                    <th class="px-4 py-3">Sản phẩm liên kết</th>
                    <th class="px-4 py-3">Thứ tự</th>
                    <th class="px-4 py-3">Thời gian hiển thị</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($banners as $banner)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-14 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                    @if($banner->image_url)
                                        <img src="{{ $banner->image_url }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <div>{{ $banner->title }}</div>
                                    @if($banner->badge_text)
                                        <span class="text-xs text-rose-600">{{ $banner->badge_text }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $banner->product?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $banner->sort_order }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $banner->starts_at?->format('d/m/Y') ?? '...' }} - {{ $banner->ends_at?->format('d/m/Y') ?? '...' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $banner->is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="inline" onsubmit="return confirm('Xóa banner này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Chưa có banner nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $banners->links() }}</div>
</x-admin-layout>
