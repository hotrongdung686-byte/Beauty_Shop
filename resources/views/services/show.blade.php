<x-shop-layout title="{{ $service->name }} - {{ config('app.name') }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="text-xs uppercase tracking-widest text-ink/40 mb-8">
            <a href="{{ route('home') }}" class="hover:text-clay-600">Trang chủ</a> /
            <a href="{{ route('services.index') }}" class="hover:text-clay-600">Dịch vụ</a> /
            <span class="text-ink/70">{{ $service->name }}</span>
        </nav>

        <div class="grid md:grid-cols-2 gap-12 lg:gap-16">
            <div class="aspect-[4/5] bg-sage-50 overflow-hidden flex items-center justify-center">
                @if($service->image)
                    <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-sage-600/40 text-7xl font-karla font-bold">{{ Str::of($service->name)->substr(0, 1) }}</span>
                @endif
            </div>

            <div class="max-w-lg">
                @if($service->category)
                    <div class="text-xs uppercase tracking-widest text-sage-700">{{ $service->category->name }}</div>
                @endif
                <h1 class="font-karla font-bold text-3xl sm:text-4xl text-ink mt-2">{{ $service->name }}</h1>

                <div class="flex items-center gap-1.5 mt-3 text-sm text-clay-600">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{{ $i <= round($service->average_rating) ? '★' : '☆' }}</span>
                    @endfor
                    <span class="text-ink/40 ml-1">({{ $service->average_rating ?: '0' }}/5 · {{ $reviews->total() }} đánh giá)</span>
                </div>

                <div class="mt-5 flex items-baseline gap-3">
                    <span class="text-2xl font-karla font-semibold text-ink">{{ number_format($service->price) }}₫</span>
                    <span class="text-ink/40 text-sm">{{ $service->duration_label }}</span>
                </div>

                @if($service->deposit_amount > 0)
                    <div class="mt-1 text-sm text-ink/50">Đặt cọc: {{ number_format($service->deposit_amount) }}₫</div>
                @endif

                @if($service->description)
                    <p class="mt-5 text-ink/70 leading-relaxed">{{ $service->description }}</p>
                @endif

                @if($service->staff->count())
                    <div class="mt-7">
                        <div class="text-xs uppercase tracking-widest text-ink/50 mb-3">Thợ thực hiện</div>
                        <div class="flex flex-wrap gap-3">
                            @foreach($service->staff as $staff)
                                <div class="flex items-center gap-2 border border-cream-300 rounded-sm pl-1.5 pr-3.5 py-1.5">
                                    @if($staff->avatar)
                                        <img src="{{ asset('storage/'.$staff->avatar) }}" alt="{{ $staff->full_name }}" class="h-7 w-7 rounded-full object-cover">
                                    @else
                                        <span class="h-7 w-7 rounded-full bg-sage-100 text-sage-700 flex items-center justify-center text-xs font-bold">{{ Str::of($staff->full_name)->substr(0, 1) }}</span>
                                    @endif
                                    <span class="text-sm text-ink/80">{{ $staff->full_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ route('booking.create', $service) }}" class="inline-flex items-center mt-9 bg-ink text-white text-sm uppercase tracking-wider px-8 py-3.5 rounded-sm hover:bg-ink/85 transition">
                    Đặt lịch ngay
                </a>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="mt-16 border-t border-cream-300 pt-10 max-w-3xl">
            <h2 class="font-karla font-bold text-xl text-ink mb-6">Đánh giá dịch vụ</h2>

            @auth
                <form action="{{ route('reviews.service.store', $service) }}" method="POST" class="border border-cream-300 p-5 mb-8">
                    @csrf
                    <div class="text-sm font-semibold text-ink mb-2">Đánh giá của bạn</div>
                    <div class="flex gap-1 mb-3" x-data="{ rating: 5 }">
                        <input type="hidden" name="rating" x-model="rating">
                        <template x-for="i in 5">
                            <button type="button" @click="rating = i" class="text-2xl" :class="i <= rating ? 'text-clay-600' : 'text-ink/20'">★</button>
                        </template>
                    </div>
                    <textarea name="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..." class="w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink"></textarea>
                    <button class="mt-3 bg-ink text-white px-5 py-2.5 rounded-sm text-sm uppercase tracking-wider hover:bg-ink/85 transition">Gửi đánh giá</button>
                </form>
            @endauth

            <div class="space-y-5">
                @forelse($reviews as $review)
                    <div class="border-b border-cream-200 pb-5">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-ink">{{ $review->user->name }}</div>
                            <div class="text-clay-600 text-sm">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                        </div>
                        <div class="text-xs text-ink/40">{{ $review->created_at->format('d/m/Y') }}</div>
                        @if($review->comment)
                            <p class="mt-2 text-ink/70 text-sm">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-ink/40 text-sm">Chưa có đánh giá nào.</p>
                @endforelse
            </div>
            <div class="mt-6">{{ $reviews->links() }}</div>
        </div>
    </div>
</x-shop-layout>
