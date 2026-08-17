<x-shop-layout title="Dịch vụ làm đẹp - {{ config('app.name') }}">
    <div class="bg-sage-50 py-12 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="text-xs uppercase tracking-widest text-sage-700 mb-2">Nghi thức làm đẹp</div>
            <h1 class="font-karla font-extrabold text-3xl sm:text-4xl text-ink">Dịch vụ làm đẹp</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
            <aside class="lg:col-span-1 space-y-8">
                <form method="GET" action="{{ route('services.index') }}" class="space-y-8">
                    <div>
                        <label class="text-xs uppercase tracking-widest text-ink/50">Tìm kiếm</label>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Tên dịch vụ..."
                               class="mt-2 w-full border-ink/20 rounded-sm text-sm focus:border-ink focus:ring-ink">
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-widest text-ink/50 mb-3">Nhóm dịch vụ</div>
                        <div class="space-y-2 text-sm">
                            <label class="flex items-center gap-2">
                                <input type="radio" name="category" value="" {{ request('category') ? '' : 'checked' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                Tất cả
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} class="text-ink focus:ring-ink" onchange="this.form.submit()">
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <button class="w-full border border-ink text-ink text-xs uppercase tracking-wider py-2.5 rounded-sm hover:bg-ink hover:text-white transition">Lọc</button>
                </form>
            </aside>

            <div class="lg:col-span-3">
                <div class="text-sm text-ink/50 mb-6 pb-4 border-b border-cream-300">{{ $services->total() }} dịch vụ</div>

                @if($services->isEmpty())
                    <div class="border border-cream-300 p-14 text-center text-ink/50">Không tìm thấy dịch vụ phù hợp.</div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-5 gap-y-10">
                        @foreach($services as $service)
                            <x-service-card :service="$service" />
                        @endforeach
                    </div>
                    <div class="mt-12">{{ $services->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-shop-layout>
