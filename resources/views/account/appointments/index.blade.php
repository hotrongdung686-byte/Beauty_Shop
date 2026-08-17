<x-shop-layout title="Lịch hẹn của tôi - {{ config('app.name') }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="font-karla font-bold text-3xl text-ink mb-8">Lịch hẹn của tôi</h1>

        @if($appointments->isEmpty())
            <div class="border border-cream-300 p-16 text-center text-ink/50">Bạn chưa có lịch hẹn nào.</div>
        @else
            <div class="space-y-4">
                @foreach($appointments as $appointment)
                    <a href="{{ route('account.appointments.show', $appointment) }}" class="block border border-cream-300 p-5 hover:border-clay-600 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-karla font-semibold text-ink">{{ $appointment->service->name }}</div>
                                <div class="text-xs text-ink/40 mt-0.5">{{ $appointment->start_at->format('H:i, d/m/Y') }} · {{ $appointment->staff?->full_name ?? 'Chưa xác định thợ' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-karla font-semibold text-ink">{{ number_format($appointment->price) }}₫</div>
                                <span class="inline-block mt-1 text-[11px] uppercase tracking-wider px-2.5 py-1 rounded-sm
                                    @class([
                                        'bg-amber-100 text-amber-700' => $appointment->status === 'pending',
                                        'bg-sky-100 text-sky-700' => $appointment->status === 'confirmed',
                                        'bg-sage-100 text-sage-700' => $appointment->status === 'completed',
                                        'bg-red-100 text-red-700' => in_array($appointment->status, ['cancelled','no_show']),
                                    ])">{{ $appointment->status_label }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $appointments->links() }}</div>
        @endif
    </div>
</x-shop-layout>
