<x-admin-layout title="Đơn hàng">
    <div class="flex items-center justify-between mb-4 gap-4 flex-wrap">
        <h1 class="text-lg font-semibold text-gray-800">Đơn hàng</h1>
        <form method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Mã đơn hàng..." class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-200 text-sm focus:border-rose-400 focus:ring-rose-400">
                <option value="">Tất cả trạng thái</option>
                @foreach(\App\Models\Order::statusLabels() as $value => $label)
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
                    <th class="px-4 py-3">Mã đơn</th>
                    <th class="px-4 py-3">Khách hàng</th>
                    <th class="px-4 py-3">Ngày đặt</th>
                    <th class="px-4 py-3">Tổng tiền</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-mono text-gray-800">{{ $order->code }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $order->user?->name ?? 'Khách vãng lai' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ number_format($order->total) }}₫</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @class([
                                    'bg-amber-100 text-amber-700' => $order->status === 'pending',
                                    'bg-blue-100 text-blue-700' => in_array($order->status, ['confirmed','processing']),
                                    'bg-indigo-100 text-indigo-700' => $order->status === 'shipping',
                                    'bg-emerald-100 text-emerald-700' => $order->status === 'completed',
                                    'bg-red-100 text-red-700' => $order->status === 'cancelled',
                                ])">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-rose-600 hover:underline">Chi tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</x-admin-layout>
