<x-shop-layout title="Đặt lịch thành công - {{ config('app.name') }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <div class="h-16 w-16 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center mx-auto text-3xl">✓</div>
        <h1 class="font-karla font-bold text-3xl text-ink mt-6">Đặt lịch thành công!</h1>
        <p class="text-ink/50 mt-2">Mã lịch hẹn: <span class="font-semibold text-ink">{{ $appointment->code }}</span></p>

        <div class="border border-cream-300 p-6 mt-10 text-left">
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Dịch vụ</span><span class="font-medium text-ink">{{ $appointment->service->name }}</span></div>
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Thời gian</span><span class="font-medium text-ink">{{ $appointment->start_at->format('H:i, d/m/Y') }}</span></div>
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Thợ thực hiện</span><span class="font-medium text-ink">{{ $appointment->staff?->full_name ?? 'Chưa xác định' }}</span></div>
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Khách hàng</span><span class="font-medium text-ink">{{ $appointment->customer_name }} - {{ $appointment->customer_phone }}</span></div>
            <div class="border-t border-cream-200 my-3"></div>
            <div class="flex justify-between font-karla font-semibold text-ink"><span>Giá dịch vụ</span><span>{{ number_format($appointment->price) }}₫</span></div>
            @if($appointment->deposit_amount > 0)
                <div class="flex justify-between text-sm text-ink/50 mt-1"><span>Đặt cọc</span><span>{{ number_format($appointment->deposit_amount) }}₫</span></div>
            @endif
        </div>

        <div class="mt-10 flex justify-center gap-3">
            <a href="{{ route('account.appointments.show', $appointment) }}" class="inline-flex items-center bg-ink text-white text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink/85 transition">Xem lịch hẹn</a>
            <a href="{{ route('services.index') }}" class="inline-flex items-center border border-ink text-ink text-sm uppercase tracking-wider px-7 py-3.5 rounded-sm hover:bg-ink hover:text-white transition">Đặt thêm dịch vụ</a>
        </div>
    </div>
</x-shop-layout>
