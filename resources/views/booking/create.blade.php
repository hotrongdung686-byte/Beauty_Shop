<x-shop-layout title="Đặt lịch {{ $service->name }} - {{ config('app.name') }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14"
         x-data="bookingForm({
            slotsUrl: '{{ route('booking.slots', $service) }}',
            csrf: '{{ csrf_token() }}',
            minDate: '{{ $minDate }}',
         })">
        <a href="{{ route('services.show', $service) }}" class="text-xs uppercase tracking-widest text-ink/40 hover:text-clay-600 transition">&larr; {{ $service->name }}</a>

        <h1 class="font-karla font-bold text-3xl text-ink mt-4 mb-1">Đặt lịch: {{ $service->name }}</h1>
        <p class="text-ink/50 mb-8">{{ number_format($service->price) }}₫ · {{ $service->duration_label }}</p>

        <form action="{{ route('booking.store', $service) }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="date" x-model="date">
            <input type="hidden" name="time" x-model="selectedTime">
            <input type="hidden" name="staff_id" x-model="selectedStaffForSubmit">

            <div class="border border-cream-300 p-6">
                <label class="text-xs uppercase tracking-widest text-ink/50">Chọn thợ thực hiện</label>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" @click="staffId = ''; fetchSlots()"
                            :class="staffId === '' ? 'border-ink bg-ink text-white' : 'border-ink/20 text-ink/70'"
                            class="px-4 py-2 border rounded-sm text-sm transition">Không yêu cầu</button>
                    @foreach($service->staff as $staff)
                        <button type="button" @click="staffId = '{{ $staff->id }}'; fetchSlots()"
                                :class="staffId === '{{ $staff->id }}' ? 'border-ink bg-ink text-white' : 'border-ink/20 text-ink/70'"
                                class="px-4 py-2 border rounded-sm text-sm transition">{{ $staff->full_name }}</button>
                    @endforeach
                </div>
            </div>

            <div class="border border-cream-300 p-6">
                <label class="text-xs uppercase tracking-widest text-ink/50">Chọn ngày</label>
                <input type="date" x-model="date" @change="fetchSlots()" min="{{ $minDate }}" max="{{ $maxDate }}"
                       class="mt-3 w-full sm:w-56 border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
            </div>

            <div class="border border-cream-300 p-6">
                <label class="text-xs uppercase tracking-widest text-ink/50">Chọn khung giờ</label>
                <div class="mt-4">
                    <template x-if="loading">
                        <p class="text-sm text-ink/40">Đang tải khung giờ...</p>
                    </template>
                    <template x-if="!loading && slots.length === 0">
                        <p class="text-sm text-ink/40">Không còn khung giờ trống trong ngày này, vui lòng chọn ngày khác.</p>
                    </template>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2" x-show="!loading && slots.length">
                        <template x-for="slot in slots" :key="slot.time">
                            <button type="button" @click="selectedTime = slot.time; selectedStaffIds = slot.staff_ids"
                                    :class="selectedTime === slot.time ? 'border-ink bg-ink text-white' : 'border-ink/20 text-ink/70 hover:border-clay-600'"
                                    class="border rounded-sm py-2.5 text-sm font-medium transition" x-text="slot.time"></button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="border border-cream-300 p-6 grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs uppercase tracking-widest text-ink/50">Họ tên</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required
                           class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest text-ink/50">Số điện thoại</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone) }}" required
                           class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-xs uppercase tracking-widest text-ink/50">Ghi chú (tuỳ chọn)</label>
                    <textarea name="note" rows="2" class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">{{ old('note') }}</textarea>
                </div>
            </div>

            <button type="submit" :disabled="!selectedTime"
                    class="w-full bg-ink text-white text-sm uppercase tracking-wider py-4 rounded-sm hover:bg-ink/85 transition disabled:opacity-40 disabled:cursor-not-allowed">
                Xác nhận đặt lịch
            </button>
        </form>
    </div>

    <script>
        function bookingForm({ slotsUrl, csrf, minDate }) {
            return {
                date: minDate,
                staffId: '',
                slots: [],
                loading: false,
                selectedTime: '',
                selectedStaffIds: [],
                get selectedStaffForSubmit() {
                    if (this.staffId) return this.staffId;
                    return this.selectedStaffIds.length ? this.selectedStaffIds[0] : '';
                },
                init() {
                    this.fetchSlots();
                },
                fetchSlots() {
                    this.loading = true;
                    this.selectedTime = '';
                    const url = new URL(slotsUrl, window.location.origin);
                    url.searchParams.set('date', this.date);
                    if (this.staffId) url.searchParams.set('staff_id', this.staffId);

                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.json())
                        .then(data => { this.slots = data.slots || []; })
                        .finally(() => { this.loading = false; });
                },
            }
        }
    </script>
</x-shop-layout>
