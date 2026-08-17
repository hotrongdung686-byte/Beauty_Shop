<x-admin-layout title="Lịch hẹn #{{ $appointment->code }}">
    <a href="{{ route('admin.appointments.index') }}" class="text-sm text-gray-500 hover:text-rose-600">&larr; Lịch hẹn</a>

    <div class="mt-4 grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Lịch hẹn #{{ $appointment->code }}</h2>
                <div class="text-sm text-gray-500 space-y-1">
                    <div>Dịch vụ: <span class="text-gray-800 font-medium">{{ $appointment->service->name }}</span></div>
                    <div>Khách hàng: {{ $appointment->customer_name }} - {{ $appointment->customer_phone }}</div>
                    @if($appointment->user)<div>Tài khoản: {{ $appointment->user->email }}</div>@endif
                    <div>Thời gian: {{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i, d/m/Y') }}</div>
                    @if($appointment->note)<div>Ghi chú: {{ $appointment->note }}</div>@endif
                </div>
                <div class="border-t border-gray-100 mt-3 pt-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Giá dịch vụ</span><span class="font-semibold">{{ number_format($appointment->price) }}₫</span></div>
                    @if($appointment->deposit_amount > 0)
                        <div class="flex justify-between text-gray-500"><span>Tiền cọc</span><span>{{ number_format($appointment->deposit_amount) }}₫</span></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Trạng thái</h2>
                <form action="{{ route('admin.appointments.status', $appointment) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        @foreach(\App\Models\Appointment::statusLabels() as $value => $label)
                            <option value="{{ $value }}" {{ $appointment->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-rose-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-rose-700">Cập nhật trạng thái</button>
                </form>
            </div>

            <div class="bg-white border border-gray-100 rounded-xl p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Phân công thợ</h2>
                <form action="{{ route('admin.appointments.assign-staff', $appointment) }}" method="POST" class="space-y-3">
                    @csrf @method('PATCH')
                    <select name="staff_id" class="w-full rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                        <option value="">-- Chọn thợ --</option>
                        @foreach($staffOptions as $staffOption)
                            <option value="{{ $staffOption->id }}" {{ $appointment->staff_id === $staffOption->id ? 'selected' : '' }}>{{ $staffOption->full_name }}</option>
                        @endforeach
                    </select>
                    <button class="w-full bg-gray-800 text-white py-2 rounded-lg text-sm font-medium hover:bg-gray-900">Phân công</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
