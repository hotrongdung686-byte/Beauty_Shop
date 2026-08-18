@php
    $methodLabels = [
        'vnpay' => 'VNPay',
        'momo' => 'Ví MoMo',
        'zalopay' => 'ZaloPay',
        'sepay' => 'Chuyển khoản QR (SePay)',
    ];
    $methodLabel = $methodLabels[$method] ?? $method;
@endphp
<x-shop-layout title="Thanh toán {{ $methodLabel }} - {{ config('app.name') }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-10">
            <div class="text-xs uppercase tracking-widest text-clay-600 mb-2">Bước cuối cùng</div>
            <h1 class="font-karla font-bold text-3xl text-ink">Thanh toán qua {{ $methodLabel }}</h1>
        </div>

        {{-- Order summary --}}
        <div class="border border-cream-300 p-6 mb-6">
            <div class="flex justify-between text-sm mb-2"><span class="text-ink/50">Mã đơn hàng</span><span class="font-karla font-semibold text-ink">#{{ $order->code }}</span></div>
            <div class="flex justify-between text-sm mb-4"><span class="text-ink/50">Số sản phẩm</span><span class="text-ink">{{ $order->items->count() }}</span></div>
            <div class="border-t border-cream-200 pt-4 flex justify-between font-karla font-bold text-lg text-ink">
                <span>Số tiền cần thanh toán</span>
                <span>{{ number_format($order->total) }}₫</span>
            </div>
        </div>

        @if($method === 'sepay')
            @if($sepayQrUrl)
                <div class="border border-cream-300 p-6 text-center mb-6">
                    <img src="{{ $sepayQrUrl }}" alt="Mã VietQR thanh toán" class="mx-auto w-64 h-64 object-contain">
                    <p class="text-sm text-ink/60 mt-4">Quét mã bằng ứng dụng ngân hàng để chuyển khoản đúng số tiền và nội dung đã điền sẵn.</p>
                    <p class="text-xs text-ink/40 mt-1">Đơn hàng sẽ tự động được xác nhận ngay khi hệ thống nhận được tiền.</p>
                </div>
                <a href="{{ route('account.orders.show', $order) }}" class="block w-full text-center bg-ink text-white text-sm uppercase tracking-wider py-4 rounded-sm hover:bg-ink/85 transition">Tôi đã chuyển khoản, xem đơn hàng</a>
            @else
                <div class="border border-amber-300 bg-amber-50 p-5 text-sm text-amber-800 mb-6">
                    <p class="font-semibold mb-1">SePay chưa được cấu hình</p>
                    <p>Cửa hàng chưa liên kết tài khoản ngân hàng thật với SePay. Vui lòng chọn phương thức thanh toán khác hoặc liên hệ quản trị viên.</p>
                </div>
            @endif
        @else
            @if($demo)
                <div class="border border-amber-300 bg-amber-50 p-5 text-sm text-amber-800 mb-6">
                    <p class="font-semibold mb-1">Chế độ demo</p>
                    <p>Cửa hàng chưa cấu hình tài khoản merchant thật cho {{ $methodLabel }}, nên hệ thống mô phỏng lại kết quả thanh toán để bạn kiểm thử toàn bộ luồng. Khi có tài khoản merchant thật, chỉ cần khai báo trong <code class="bg-amber-100 px-1 rounded">.env</code> là cổng thanh toán thật sẽ tự động được sử dụng.</p>
                </div>
            @else
                <div class="border border-sage-600/40 bg-sage-50 p-5 text-sm text-sage-700 mb-6">
                    Bạn sẽ được chuyển đến cổng thanh toán {{ $methodLabel }} để hoàn tất giao dịch.
                </div>
            @endif

            <div class="mt-6 space-y-3">
                @if(!$demo)
                    <form action="{{ route('payment.gateway.'.$method, $order) }}" method="POST">
                        @csrf
                        <button class="w-full bg-ink text-white text-sm uppercase tracking-wider py-4 rounded-sm hover:bg-ink/85 transition">Tiếp tục đến {{ $methodLabel }}</button>
                    </form>
                @else
                    <form action="{{ route('payment.gateway.simulate', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="outcome" value="success">
                        <button class="w-full bg-ink text-white text-sm uppercase tracking-wider py-4 rounded-sm hover:bg-ink/85 transition">Giả lập thanh toán thành công</button>
                    </form>
                    <form action="{{ route('payment.gateway.simulate', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="outcome" value="failed">
                        <button class="w-full border border-red-300 text-red-600 text-sm uppercase tracking-wider py-4 rounded-sm hover:bg-red-50 transition">Giả lập thanh toán thất bại</button>
                    </form>
                @endif
            </div>
        @endif

        <div class="text-center mt-8">
            <a href="{{ route('account.orders.show', $order) }}" class="text-xs uppercase tracking-widest text-ink/40 hover:text-clay-600 transition">Xem đơn hàng &rarr;</a>
        </div>
    </div>
</x-shop-layout>
