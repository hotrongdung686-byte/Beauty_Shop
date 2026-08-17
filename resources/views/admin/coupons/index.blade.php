<x-admin-layout title="Mã giảm giá">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Mã giảm giá</h1>
        <a href="{{ route('admin.coupons.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">+ Thêm mã</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Mã</th>
                    <th class="px-4 py-3">Loại</th>
                    <th class="px-4 py-3">Giá trị</th>
                    <th class="px-4 py-3">Đã dùng</th>
                    <th class="px-4 py-3">Hiệu lực</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($coupons as $coupon)
                    <tr>
                        <td class="px-4 py-3 font-mono font-medium text-gray-800">{{ $coupon->code }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $coupon->type === 'percent' ? 'Phần trăm' : 'Số tiền' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $coupon->type === 'percent' ? $coupon->value.'%' : number_format($coupon->value).'₫' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $coupon->used_count }}{{ $coupon->usage_limit ? '/'.$coupon->usage_limit : '' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $coupon->starts_at?->format('d/m/Y') ?? '...' }} - {{ $coupon->ends_at?->format('d/m/Y') ?? '...' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $coupon->isValidNow() ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $coupon->isValidNow() ? 'Còn hiệu lực' : 'Hết hiệu lực' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Xóa mã này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $coupons->links() }}</div>
</x-admin-layout>
