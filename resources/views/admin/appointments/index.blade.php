<x-admin-layout title="Lịch hẹn">
    <div class="flex items-center justify-between mb-4 gap-4 flex-wrap">
        <h1 class="text-lg font-semibold text-gray-800">Lịch hẹn</h1>
        <form method="GET" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <option value="">Tất cả trạng thái</option>
                @foreach(\App\Models\Appointment::statusLabels() as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="bg-gray-800 text-white px-3 py-2 rounded-lg text-sm hover:bg-gray-900">Lọc</button>
        </form>
    </div>

    <div class="bg-white border border-gray-100 rounded-xl overflow-hidden overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Mã</th>
                    <th class="px-4 py-3">Dịch vụ</th>
                    <th class="px-4 py-3">Khách hàng</th>
                    <th class="px-4 py-3">Thợ</th>
                    <th class="px-4 py-3">Thời gian</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($appointments as $appointment)
                    <tr>
                        <td class="px-4 py-3 font-mono text-gray-800">{{ $appointment->code }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $appointment->service->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $appointment->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $appointment->staff?->full_name ?? 'Chưa xác định' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $appointment->start_at->format('H:i d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @class([
                                    'bg-amber-100 text-amber-700' => $appointment->status === 'pending',
                                    'bg-blue-100 text-blue-700' => $appointment->status === 'confirmed',
                                    'bg-emerald-100 text-emerald-700' => $appointment->status === 'completed',
                                    'bg-red-100 text-red-700' => in_array($appointment->status, ['cancelled','no_show']),
                                ])">{{ $appointment->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.appointments.show', $appointment) }}" class="text-rose-600 hover:underline">Chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $appointments->links() }}</div>
</x-admin-layout>
