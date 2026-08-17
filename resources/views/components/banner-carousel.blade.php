@props(['banners'])

<section class="bg-cream-50"
         x-data="{
            active: 0,
            count: {{ $banners->count() }},
            timer: null,
            next() { this.active = (this.active + 1) % this.count },
            prev() { this.active = (this.active - 1 + this.count) % this.count },
            go(i) { this.active = i },
            start() { this.timer = setInterval(() => this.next(), 5500) },
            stop() { clearInterval(this.timer) },
         }"
         x-init="start()"
         @mouseenter="stop()" @mouseleave="start()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="relative">
            @foreach($banners as $i => $banner)
                <div x-show="active === {{ $i }}"
                     class="{{ $i === 0 ? '' : 'md:absolute md:inset-0' }}">
                    <a href="{{ $banner->target_url }}" class="grid md:grid-cols-2 gap-6 md:gap-10 items-center">
                        <div class="aspect-[4/5] sm:aspect-[16/10] md:aspect-[4/5] overflow-hidden rounded-sm order-1"
                             style="background-color: {{ $banner->background_color ?: '#E5DDC8' }};">
                            @if($banner->image_url)
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-ink/20 font-karla font-bold text-6xl">{{ Str::of($banner->title)->substr(0, 1) }}</div>
                            @endif
                        </div>

                        <div class="order-2">
                            @if($banner->badge_text)
                                <span class="inline-block bg-clay-600 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-sm mb-4">{{ $banner->badge_text }}</span>
                            @endif
                            <h2 class="font-karla font-extrabold text-2xl sm:text-3xl lg:text-4xl leading-tight tracking-tight text-ink">{{ $banner->title }}</h2>
                            @if($banner->subtitle)
                                <p class="mt-3 text-sm sm:text-base text-ink/60 max-w-md">{{ $banner->subtitle }}</p>
                            @endif
                            @if($banner->display_price)
                                <div class="mt-4 text-xl sm:text-2xl font-karla font-semibold text-ink">{{ number_format($banner->display_price) }}₫</div>
                            @endif
                            <span class="inline-flex items-center mt-6 bg-ink text-white text-sm uppercase tracking-wider px-6 py-3 rounded-sm hover:bg-ink/85 transition">
                                {{ $banner->button_text }}
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        @if($banners->count() > 1)
            <div class="flex items-center justify-center md:justify-start gap-4 mt-8">
                <button @click="prev()" aria-label="Trước" class="h-9 w-9 rounded-full border border-ink/15 hover:border-ink flex items-center justify-center text-ink transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="flex items-center gap-2">
                    @foreach($banners as $i => $banner)
                        <button @click="go({{ $i }})" aria-label="Slide {{ $i + 1 }}"
                                class="h-1.5 rounded-full transition-all"
                                :class="active === {{ $i }} ? 'w-7 bg-ink' : 'w-1.5 bg-ink/20'"></button>
                    @endforeach
                </div>
                <button @click="next()" aria-label="Sau" class="h-9 w-9 rounded-full border border-ink/15 hover:border-ink flex items-center justify-center text-ink transition">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        @endif
    </div>
</section>
