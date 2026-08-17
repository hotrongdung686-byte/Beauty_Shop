<x-shop-layout title="Lịch hẹn {{ $appointment->code }} - {{ config('app.name') }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <a href="{{ route('account.appointments.index') }}" class="text-xs uppercase tracking-widest text-ink/40 hover:text-clay-600 transition">&larr; Lịch hẹn của tôi</a>

        <div class="flex items-center justify-between mt-4 mb-8">
            <h1 class="font-karla font-bold text-3xl text-ink">Lịch hẹn #{{ $appointment->code }}</h1>
            <span class="text-[11px] uppercase tracking-wider px-3 py-1.5 rounded-sm
                @class([
                    'bg-amber-100 text-amber-700' => $appointment->status === 'pending',
                    'bg-sky-100 text-sky-700' => $appointment->status === 'confirmed',
                    'bg-sage-100 text-sage-700' => $appointment->status === 'completed',
                    'bg-red-100 text-red-700' => in_array($appointment->status, ['cancelled','no_show']),
                ])">{{ $appointment->status_label }}</span>
        </div>

        <div class="border border-cream-300 p-6 space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-ink/50">Dịch vụ</span><span class="font-medium text-ink">{{ $appointment->service->name }}</span></div>
            <div class="flex justify-between"><span class="text-ink/50">Thời gian</span><span class="font-medium text-ink">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i, d/m/Y') }}</span></div>
            <div class="flex justify-between"><span class="text-ink/50">Thợ thực hiện</span><span class="font-medium text-ink">{{ $appointment->staff?->full_name ?? 'Chưa xác định' }}</span></div>
            <div class="flex justify-between"><span class="text-ink/50">Khách hàng</span><span class="font-medium text-ink">{{ $appointment->customer_name }} - {{ $appointment->customer_phone }}</span></div>
            @if($appointment->note)
                <div class="flex justify-between"><span class="text-ink/50">Ghi chú</span><span class="font-medium text-ink text-right">{{ $appointment->note }}</span></div>
            @endif
            <div class="border-t border-cream-200 my-2"></div>
            <div class="flex justify-between font-karla font-semibold text-ink"><span>Giá dịch vụ</span><span>{{ number_format($appointment->price) }}₫</span></div>
            @if($appointment->deposit_amount > 0)
                <div class="flex justify-between text-ink/50"><span>Đặt cọc</span><span>{{ number_format($appointment->deposit_amount) }}₫</span></div>
            @endif
        </div>

        @if($appointment->canBeCancelled())
            <form action="{{ route('account.appointments.cancel', $appointment) }}" method="POST" class="mt-8" onsubmit="return confirm('Bạn chắc chắn muốn hủy lịch hẹn này?')">
                @csrf
                <button class="border border-red-300 text-red-600 px-6 py-3 rounded-sm text-sm uppercase tracking-wider hover:bg-red-50 transition">Hủy lịch hẹn</button>
            </form>
        @endif
    </div>
</x-shop-layout>
