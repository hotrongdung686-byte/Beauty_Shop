@props(['service'])

<div class="group">
    <a href="{{ route('services.show', $service) }}" class="block aspect-[4/5] bg-sage-50 relative overflow-hidden">
        @if($service->image)
            <img src="{{ asset('storage/'.$service->image) }}" alt="{{ $service->name }}" class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-500 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center text-sage-600/50 text-5xl font-karla font-bold">
                {{ Str::of($service->name)->substr(0, 1) }}
            </div>
        @endif
        @if($service->is_featured)
            <span class="absolute top-3 left-3 bg-white/90 text-ink text-[10px] uppercase tracking-wider px-2.5 py-1 rounded-sm">Nổi bật</span>
        @endif
    </a>
    <div class="pt-3.5 text-center">
        @if($service->category)
            <div class="text-[11px] uppercase tracking-widest text-ink/40 mb-1">{{ $service->category->name }}</div>
        @endif
        <a href="{{ route('services.show', $service) }}" class="block font-karla font-semibold text-ink hover:text-clay-600 transition line-clamp-2">
            {{ $service->name }}
        </a>
        <div class="mt-1.5 text-sm text-ink/70">{{ number_format($service->price) }}₫ · {{ $service->duration_label }}</div>
    </div>
</div>
