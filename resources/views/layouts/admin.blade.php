<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Quản trị' }} - {{ config('app.name', 'BeautyShop') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f4f7fe] text-slate-700">
<div x-data="{ mobileNav: false }" class="min-h-screen lg:flex">

    @php
        $navItems = [
            ['label' => 'Tổng quan', 'icon' => 'home', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard'],
            ['label' => 'Banner', 'icon' => 'sparkles', 'route' => 'admin.banners.index', 'match' => 'admin.banners.*'],
            ['label' => 'Sản phẩm', 'icon' => 'box', 'route' => 'admin.products.index', 'match' => 'admin.products.*'],
            ['label' => 'Danh mục SP', 'icon' => 'tag', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*'],
            ['label' => 'Thương hiệu', 'icon' => 'collection', 'route' => 'admin.brands.index', 'match' => 'admin.brands.*'],
            ['label' => 'Đơn hàng', 'icon' => 'bag', 'route' => 'admin.orders.index', 'match' => 'admin.orders.*'],
            ['label' => 'Mã giảm giá', 'icon' => 'ticket', 'route' => 'admin.coupons.index', 'match' => 'admin.coupons.*'],
            ['label' => 'Dịch vụ', 'icon' => 'sparkles', 'route' => 'admin.services.index', 'match' => 'admin.services.*'],
            ['label' => 'Danh mục DV', 'icon' => 'collection', 'route' => 'admin.service-categories.index', 'match' => 'admin.service-categories.*'],
            ['label' => 'Nhân viên / Thợ', 'icon' => 'users', 'route' => 'admin.staff.index', 'match' => 'admin.staff.*'],
            ['label' => 'Lịch hẹn', 'icon' => 'calendar', 'route' => 'admin.appointments.index', 'match' => 'admin.appointments.*'],
        ];
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
    @endphp

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-72 shrink-0 px-5 py-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1 px-2 pb-6">
            <span class="font-extrabold text-lg tracking-tight text-slate-800">BEAUTY<span class="text-indigo-600">SHOP</span></span>
        </a>

        <nav class="flex-1 space-y-1.5">
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group relative flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition
                          {{ $active ? 'bg-white text-indigo-600 shadow-[0_4px_18px_-4px_rgba(79,70,229,0.25)]' : 'text-slate-500 hover:text-indigo-600' }}">
                    <x-admin.icon name="{{ $item['icon'] }}" class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}" />
                    {{ $item['label'] }}
                    @if($active)
                        <span class="absolute right-1.5 top-1/2 -translate-y-1/2 h-6 w-1 rounded-full bg-indigo-600"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        <a href="{{ route('home') }}" target="_blank" class="mt-6 block rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 p-5 text-center text-white overflow-hidden relative">
            <div class="mx-auto mb-3 h-12 w-12 rounded-full bg-white/15 flex items-center justify-center">
                <x-admin.icon name="external" class="h-6 w-6" />
            </div>
            <div class="font-bold text-sm">Xem cửa hàng</div>
            <p class="text-xs text-indigo-100 mt-1 leading-relaxed">Mở giao diện khách hàng ở tab mới để xem trực tiếp thay đổi.</p>
            <span class="mt-4 inline-block w-full rounded-full bg-white/15 py-2 text-xs font-semibold hover:bg-white/25 transition">Mở ngay</span>
        </a>
    </aside>

    <div class="flex-1 min-w-0 px-4 sm:px-6 lg:pr-8 lg:py-6">

        {{-- Topbar --}}
        <header class="flex items-center justify-between gap-4 py-4 lg:py-0 lg:mb-6">
            <div class="flex items-center gap-3">
                <button @click="mobileNav = !mobileNav" class="lg:hidden p-2 -ml-2 text-slate-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <div>
                    <div class="text-xs text-slate-400">Trang quản trị / <span class="text-slate-500">{{ $title ?? 'Tổng quan' }}</span></div>
                    <h1 class="text-xl font-bold text-slate-800">{{ $title ?? 'Tổng quan' }}</h1>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <label class="hidden md:flex items-center gap-2 bg-white rounded-full pl-4 pr-1 py-1.5 shadow-[0_4px_18px_-8px_rgba(17,24,39,0.15)]">
                    <x-admin.icon name="search" class="h-4 w-4 text-slate-400" />
                    <input type="text" placeholder="Tìm kiếm..." class="border-0 focus:ring-0 text-sm p-0 w-32 placeholder:text-slate-400">
                </label>

                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="relative h-10 w-10 shrink-0 rounded-full bg-white shadow-[0_4px_18px_-8px_rgba(17,24,39,0.15)] flex items-center justify-center text-slate-500 hover:text-indigo-600">
                    <x-admin.icon name="bell" class="h-5 w-5" />
                    @if($pendingOrders > 0)
                        <span class="absolute -top-1 -right-1 h-4 min-w-4 px-1 rounded-full bg-rose-500 text-white text-[10px] leading-4 text-center">{{ $pendingOrders }}</span>
                    @endif
                </a>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2">
                        <span class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 text-white flex items-center justify-center text-sm font-bold shadow-[0_4px_18px_-8px_rgba(79,70,229,0.5)]">
                            {{ Str::of(auth()->user()->name)->substr(0, 1) }}
                        </span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-lg ring-1 ring-black/5 py-2 text-sm z-20">
                        <div class="px-4 py-2 border-b border-slate-100">
                            <div class="font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-400">{{ auth()->user()->role }}</div>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-slate-50">Tài khoản</a>
                        <a href="{{ route('home') }}" class="block px-4 py-2 hover:bg-slate-50">Về trang khách hàng</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-slate-50 text-rose-600">
                                <x-admin.icon name="logout" class="h-4 w-4" /> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div x-show="mobileNav" x-cloak class="lg:hidden bg-white rounded-2xl shadow mb-4 px-3 py-3 space-y-1 text-sm">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2 font-medium {{ request()->routeIs($item['match']) ? 'bg-indigo-50 text-indigo-600' : 'text-slate-500' }}">
                    <x-admin.icon name="{{ $item['icon'] }}" class="h-5 w-5" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>

        <main class="pb-10">
            @if(session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 mb-4">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm px-4 py-3 mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
