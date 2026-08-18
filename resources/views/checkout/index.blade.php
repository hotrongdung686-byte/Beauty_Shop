@php
    $coupon = session('checkout_coupon') ? \App\Models\Coupon::where('code', session('checkout_coupon'))->first() : null;
    $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
    $shippingFee = $subtotal >= 500000 ? 0 : 30000;
    $total = max($subtotal - $discount + $shippingFee, 0);
@endphp
<x-shop-layout title="Thanh toán - {{ config('app.name') }}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="font-karla font-bold text-3xl text-ink mb-8">Thanh toán</h1>

        <form action="{{ route('checkout.store') }}" method="POST" class="grid md:grid-cols-3 gap-8">
            @csrf
            <div class="md:col-span-2 space-y-6">
                <div class="border border-cream-300 p-6">
                    <h2 class="font-karla font-semibold text-ink mb-5">Thông tin giao hàng</h2>

                    @if($addresses->count())
                        <div class="mb-4">
                            <label class="text-xs uppercase tracking-widest text-ink/50">Chọn địa chỉ đã lưu</label>
                            <select id="address-select" class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                                <option value="">-- Nhập địa chỉ mới --</option>
                                @foreach($addresses as $addr)
                                    <option value="{{ $addr->id }}"
                                            data-recipient="{{ $addr->recipient }}"
                                            data-phone="{{ $addr->phone }}"
                                            data-address="{{ $addr->full_address }}">
                                        {{ $addr->recipient }} - {{ $addr->phone }} ({{ $addr->full_address }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs uppercase tracking-widest text-ink/50">Họ tên người nhận</label>
                            <input id="ship_recipient" type="text" name="ship_recipient" value="{{ old('ship_recipient', auth()->user()->name) }}" required
                                   class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                        </div>
                        <div>
                            <label class="text-xs uppercase tracking-widest text-ink/50">Số điện thoại</label>
                            <input id="ship_phone" type="text" name="ship_phone" value="{{ old('ship_phone', auth()->user()->phone) }}" required
                                   class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-xs uppercase tracking-widest text-ink/50">Địa chỉ giao hàng</label>
                        <textarea id="ship_address" name="ship_address" rows="2" required
                                  class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">{{ old('ship_address') }}</textarea>
                    </div>
                    <div class="mt-4">
                        <label class="text-xs uppercase tracking-widest text-ink/50">Ghi chú (tuỳ chọn)</label>
                        <textarea name="note" rows="2" class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="border border-cream-300 p-6">
                    <h2 class="font-karla font-semibold text-ink mb-5">Phương thức thanh toán</h2>
                    <div class="space-y-3 text-sm">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="cod" checked class="text-ink focus:ring-ink">
                            Thanh toán khi nhận hàng (COD)
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="bank_transfer" class="text-ink focus:ring-ink">
                            Chuyển khoản ngân hàng
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="momo" class="text-ink focus:ring-ink">
                            Ví MoMo
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="vnpay" class="text-ink focus:ring-ink">
                            VNPay
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="zalopay" class="text-ink focus:ring-ink">
                            ZaloPay
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="payment_method" value="sepay" class="text-ink focus:ring-ink">
                            Chuyển khoản QR (SePay)
                        </label>
                    </div>
                </div>
            </div>

            <div class="md:col-span-1">
                <div class="border border-cream-300 p-6 sticky top-24">
                    <h2 class="font-karla font-semibold text-ink mb-5">Đơn hàng ({{ $items->count() }} sản phẩm)</h2>
                    <div class="space-y-2 max-h-56 overflow-y-auto text-sm mb-5">
                        @foreach($items as $line)
                            <div class="flex justify-between text-ink/60">
                                <span class="line-clamp-1">{{ $line['variant']->product->name }} x{{ $line['quantity'] }}</span>
                                <span class="shrink-0 ml-2">{{ number_format($line['line_total']) }}₫</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-cream-200 pt-4">
                        @if($coupon)
                            <div class="flex justify-between text-sage-700 text-sm mb-1">
                                <span>Mã {{ $coupon->code }}</span>
                                <span>-{{ number_format($discount) }}₫</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-ink/60 text-sm mb-1">
                            <span>Tạm tính</span>
                            <span>{{ number_format($subtotal) }}₫</span>
                        </div>
                        <div class="flex justify-between text-ink/60 text-sm mb-1">
                            <span>Phí vận chuyển</span>
                            <span>{{ $shippingFee > 0 ? number_format($shippingFee).'₫' : 'Miễn phí' }}</span>
                        </div>
                        <div class="flex justify-between font-karla font-semibold text-ink text-base mt-3 border-t border-cream-200 pt-3">
                            <span>Tổng cộng</span>
                            <span>{{ number_format($total) }}₫</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-5 bg-ink text-white text-sm uppercase tracking-wider py-3.5 rounded-sm hover:bg-ink/85 transition">Đặt hàng</button>
                </div>
            </div>
        </form>

        <div class="mt-6 max-w-md">
            @if($coupon)
                <form action="{{ route('checkout.coupon.remove') }}" method="POST" class="flex gap-2">
                    @csrf
                    <div class="flex-1 border border-sage-600/40 bg-sage-50 text-sage-700 text-sm px-3 py-2.5 rounded-sm">Đang áp dụng: {{ $coupon->code }}</div>
                    <button class="text-sm text-ink/50 hover:text-red-500 px-3 transition">Bỏ mã</button>
                </form>
            @else
                <form action="{{ route('checkout.coupon.apply') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="Mã giảm giá" class="flex-1 border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                    <button class="bg-ink text-white px-5 rounded-sm text-sm uppercase tracking-wider hover:bg-ink/85 transition">Áp dụng</button>
                </form>
            @endif
        </div>
    </div>

    <script>
        const select = document.getElementById('address-select');
        if (select) {
            select.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                if (!opt.value) return;
                document.getElementById('ship_recipient').value = opt.dataset.recipient || '';
                document.getElementById('ship_phone').value = opt.dataset.phone || '';
                document.getElementById('ship_address').value = opt.dataset.address || '';
            });
        }
    </script>
</x-shop-layout>
