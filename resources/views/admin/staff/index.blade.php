<x-admin-layout title="Nhân viên / Thợ">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-lg font-semibold text-gray-800">Nhân viên / Thợ</h1>
        <a href="{{ route('admin.staff.create') }}" class="bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-rose-700">+ Thêm nhân viên</a>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Họ tên</th>
                    <th class="px-4 py-3">Điện thoại</th>
                    <th class="px-4 py-3">Số lịch hẹn</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($staff as $member)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-gray-100 overflow-hidden shrink-0">
                                    @if($member->avatar)
                                        <img src="{{ asset('storage/'.$member->avatar) }}" class="w-full h-full object-cover">
                                    @endif
                                </div>
                                {{ $member->full_name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $member->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $member->appointments_count }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $member->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $member->is_active ? 'Hoạt động' : 'Ẩn' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.staff.edit', $member) }}" class="text-rose-600 hover:underline">Sửa</a>
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Xóa nhân viên này?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-400 hover:text-red-500">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $staff->links() }}</div>
</x-admin-layout>
