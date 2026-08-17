@php
    $statusMeta = [
        'pending' => ['label' => 'Chờ xác nhận', 'color' => '#f59e0b'],
        'confirmed' => ['label' => 'Đã xác nhận', 'color' => '#3b82f6'],
        'processing' => ['label' => 'Đang xử lý', 'color' => '#6366f1'],
        'shipping' => ['label' => 'Đang giao', 'color' => '#8b5cf6'],
        'completed' => ['label' => 'Hoàn tất', 'color' => '#10b981'],
        'cancelled' => ['label' => 'Đã hủy', 'color' => '#f43f5e'],
    ];
    $totalOrders = max(1, $orderStatusCounts->sum());
    $cumulative = 0;
    $gradientStops = [];
    $legend = [];
    foreach ($statusMeta as $key => $meta) {
        $count = $orderStatusCounts[$key] ?? 0;
        if ($count <= 0) {
            continue;
        }
        $percent = round(($count / $totalOrders) * 100, 1);
        $start = $cumulative;
        $cumulative += $percent;
        $gradientStops[] = "{$meta['color']} {$start}% {$cumulative}%";
        $legend[] = ['label' => $meta['label'], 'color' => $meta['color'], 'percent' => $percent, 'count' => $count];
    }
    $conicGradient = $gradientStops ? implode(', ', $gradientStops) : '#e2e8f0 0% 100%';

    $maxMonthly = max(1, $monthlyRevenue->max('total'));
    $monthCount = max(1, $monthlyRevenue->count() - 1);
    $linePoints = $monthlyRevenue->values()->map(function ($m, $i) use ($maxMonthly, $monthCount) {
        $x = round(($i / $monthCount) * 580 + 10, 1);
        $y = round(190 - ($m['total'] / $maxMonthly) * 170, 1);
        return ['x' => $x, 'y' => $y, 'label' => $m['label']];
    });
    $linePointsStr = $linePoints->map(fn ($p) => "{$p['x']},{$p['y']}")->implode(' ');
    $areaPointsStr = '10,190 '.$linePointsStr.' 590,190';

    $maxWeekly = max(1, $weeklyRevenue->max('total'));
@endphp
<x-admin-layout title="Tổng quan">

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        @foreach([
            ['icon' => 'chart', 'label' => 'Doanh thu hôm nay', 'value' => number_format($todayRevenue).'₫'],
            ['icon' => 'bag', 'label' => 'Doanh thu tháng này', 'value' => number_format($monthRevenue).'₫'],
            ['icon' => 'ticket', 'label' => 'Đơn chờ xử lý', 'value' => $ordersPending],
            ['icon' => 'calendar', 'label' => 'Lịch hẹn hôm nay', 'value' => $appointmentsToday],
            ['icon' => 'box', 'label' => 'Tổng sản phẩm', 'value' => $productCount],
            ['icon' => 'users', 'label' => 'Khách mới hôm nay', 'value' => $customersToday],
        ] as $card)
            <div class="bg-white rounded-2xl p-4 flex items-center gap-3 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)]">
                <div class="h-12 w-12 shrink-0 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <x-admin.icon name="{{ $card['icon'] }}" class="h-6 w-6 text-indigo-600" />
                </div>
                <div class="min-w-0">
                    <div class="text-xs text-slate-400 truncate">{{ $card['label'] }}</div>
                    <div class="text-lg font-bold text-slate-800 truncate">{{ $card['value'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-3 gap-5 mb-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)]">
            <div class="flex items-start justify-between">
                <div>
                    <div class="text-xs text-slate-400">6 tháng gần đây</div>
                    <div class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($monthlyRevenue->last()['total'] ?? 0) }}₫</div>
                    <div class="text-xs text-slate-400 mt-0.5">Doanh thu tháng này</div>
                </div>
                <div class="h-9 w-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-admin.icon name="chart" class="h-4 w-4 text-indigo-600" />
                </div>
            </div>

            <svg viewBox="0 0 600 210" class="w-full h-48 mt-2">
                <defs>
                    <linearGradient id="lineFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#4f46e5" stop-opacity="0.18" />
                        <stop offset="100%" stop-color="#4f46e5" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <polygon points="{{ $areaPointsStr }}" fill="url(#lineFill)" />
                <polyline points="{{ $linePointsStr }}" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                @foreach($linePoints as $p)
                    <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="white" stroke="#4f46e5" stroke-width="2.5" />
                @endforeach
            </svg>
            <div class="flex justify-between text-xs text-slate-400 px-1">
                @foreach($linePoints as $p)
                    <span>{{ $p['label'] }}</span>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)] flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div class="font-bold text-slate-800">Doanh thu 7 ngày qua</div>
                <div class="h-9 w-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-admin.icon name="chart" class="h-4 w-4 text-indigo-600" />
                </div>
            </div>
            <div class="flex-1 flex items-end justify-between gap-2 h-40">
                @foreach($weeklyRevenue as $day)
                    @php $h = max(6, round(($day['total'] / $maxWeekly) * 100)); @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                        <div class="w-full max-w-[18px] rounded-full bg-gradient-to-t from-indigo-600 to-sky-300" style="height: {{ $h }}%"></div>
                        <span class="text-[11px] text-slate-400">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Table + stat + donut --}}
    <div class="grid lg:grid-cols-4 gap-5">
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)]">
            <div class="flex items-center justify-between mb-4">
                <div class="font-bold text-slate-800">Đơn hàng gần đây</div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">Xem tất cả</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-slate-400 border-b border-slate-100">
                            <th class="pb-2 font-medium">Mã đơn</th>
                            <th class="pb-2 font-medium">Khách hàng</th>
                            <th class="pb-2 font-medium">Trạng thái</th>
                            <th class="pb-2 font-medium text-right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="py-2.5">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-slate-700 hover:text-indigo-600">{{ $order->code }}</a>
                                </td>
                                <td class="py-2.5 text-slate-500">{{ $order->user?->name ?? 'Khách vãng lai' }}</td>
                                <td class="py-2.5">
                                    <span class="text-xs px-2 py-0.5 rounded-full"
                                          style="background: {{ ($statusMeta[$order->status]['color'] ?? '#94a3b8') }}1a; color: {{ $statusMeta[$order->status]['color'] ?? '#64748b' }}">
                                        {{ $statusMeta[$order->status]['label'] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-right font-semibold text-slate-800">{{ number_format($order->total) }}₫</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400">Chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)] flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="font-bold text-slate-800 text-sm">Lịch hẹn hôm nay</div>
                <div class="h-9 w-9 rounded-lg bg-indigo-50 flex items-center justify-center">
                    <x-admin.icon name="calendar" class="h-4 w-4 text-indigo-600" />
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-bold text-slate-800">{{ $appointmentsToday }}</div>
                <div class="text-xs text-slate-400 mt-1">lịch hẹn</div>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($upcomingAppointments->take(3) as $appointment)
                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="flex items-center justify-between text-xs hover:text-indigo-600">
                        <span class="text-slate-500">{{ $appointment->start_at->format('H:i d/m') }} · {{ $appointment->service->name }}</span>
                    </a>
                @empty
                    <p class="text-xs text-slate-400">Không có lịch hẹn sắp tới.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-[0_4px_18px_-10px_rgba(17,24,39,0.15)]">
            <div class="flex items-center justify-between mb-4">
                <div class="font-bold text-slate-800 text-sm">Đơn hàng theo trạng thái</div>
                <span class="text-xs text-slate-400">{{ $orderStatusCounts->sum() }} đơn</span>
            </div>
            <div class="mx-auto h-32 w-32 rounded-full flex items-center justify-center" style="background: conic-gradient({{ $conicGradient }})">
                <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center flex-col">
                    <span class="text-lg font-bold text-slate-800">{{ $orderStatusCounts->sum() }}</span>
                    <span class="text-[10px] text-slate-400">đơn hàng</span>
                </div>
            </div>
            <div class="mt-4 space-y-1.5">
                @foreach($legend as $item)
                    <div class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-1.5 text-slate-500">
                            <span class="h-2 w-2 rounded-full" style="background: {{ $item['color'] }}"></span>
                            {{ $item['label'] }}
                        </span>
                        <span class="text-slate-700 font-medium">{{ $item['percent'] }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
