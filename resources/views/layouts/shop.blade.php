<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'BeautyShop') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=karla:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-karla antialiased bg-white text-ink">
<div x-data="{ mobileNav: false }" class="min-h-screen flex flex-col">

    {{-- Utility marquee bar --}}
    <div class="bg-ink text-cream-100 text-xs tracking-[0.15em] uppercase overflow-hidden">
        <div class="flex items-center gap-10 py-2 px-4 whitespace-nowrap animate-[marquee_22s_linear_infinite]">
            @for($i = 0; $i < 2; $i++)
                <span>Miễn phí vận chuyển từ 500.000₫</span>
                <span class="opacity-40">·</span>
                <span>Đặt lịch làm đẹp trong 1 phút</span>
                <span class="opacity-40">·</span>
                <span>Mỹ phẩm chính hãng 100%</span>
                <span class="opacity-40">·</span>
            @endfor
        </div>
    </div>

    <header class="bg-white/95 backdrop-blur border-b border-cream-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 items-center h-20 gap-4">
                <nav class="hidden md:flex items-center gap-7 text-sm">
                    <a href="{{ route('products.index') }}" class="hover:text-clay-600 transition {{ request()->routeIs('products.*') ? 'text-clay-600' : 'text-ink/70' }}">Mỹ phẩm</a>
                    <a href="{{ route('services.index') }}" class="hover:text-clay-600 transition {{ request()->routeIs('services.*') ? 'text-clay-600' : 'text-ink/70' }}">Dịch vụ</a>
                    @auth
                        <a href="{{ route('account.orders.index') }}" class="hover:text-clay-600 transition {{ request()->routeIs('account.orders.*') ? 'text-clay-600' : 'text-ink/70' }}">Đơn hàng</a>
                    @endauth
                </nav>

                <a href="{{ route('home') }}" class="justify-self-center text-center leading-none">
                    <span class="font-karla font-extrabold text-2xl sm:text-3xl tracking-tight">{{ config('app.name', 'BeautyShop') }}</span>
                </a>

                <div class="flex items-center justify-end gap-4 sm:gap-5">
                    <a href="{{ route('services.index') }}" class="hidden lg:inline-flex items-center text-xs uppercase tracking-wider border border-ink rounded-sm px-4 py-2 hover:bg-ink hover:text-white transition">
                        Đặt lịch ngay
                    </a>

                    @auth
                        <div x-data="{ open: false }" class="relative hidden sm:block">
                            <button @click="open = !open" class="p-1.5 text-ink/80 hover:text-clay-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 mt-3 w-56 bg-white border border-cream-200 shadow-lg rounded-sm py-1 text-sm">
                                @if(auth()->user()->role !== 'customer')
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-cream-100">Trang quản trị</a>
                                @endif
                                <a href="{{ route('account.orders.index') }}" class="block px-4 py-2 hover:bg-cream-100">Đơn hàng của tôi</a>
                                <a href="{{ route('account.appointments.index') }}" class="block px-4 py-2 hover:bg-cream-100">Lịch hẹn của tôi</a>
                                <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 hover:bg-cream-100">Yêu thích</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-cream-100">Tài khoản</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 hover:bg-cream-100">Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline text-sm text-ink/70 hover:text-clay-600 transition">Đăng nhập</a>
                    @endauth

                    <a href="{{ route('cart.index') }}" class="relative p-1.5 text-ink/80 hover:text-clay-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.876-4.7 2.267-7.15a.75.75 0 00-.727-.85H5.106M7.5 14.25L5.106 5.25M7.5 14.25l-1.5 6M14.25 18.75a.75.75 0 100 1.5.75.75 0 000-1.5zm-6 0a.75.75 0 100 1.5.75.75 0 000-1.5z" />
                        </svg>
                        @if(($cartCount ?? 0) > 0)
                            <span class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-clay-600 text-white text-[10px] leading-4 text-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <button @click="mobileNav = !mobileNav" class="md:hidden p-1.5 text-ink/80">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileNav" x-cloak class="md:hidden border-t border-cream-200 px-4 py-4 space-y-3 bg-white">
            <a href="{{ route('products.index') }}" class="block text-sm">Mỹ phẩm</a>
            <a href="{{ route('services.index') }}" class="block text-sm">Dịch vụ</a>
            @auth
                <a href="{{ route('account.orders.index') }}" class="block text-sm">Đơn hàng của tôi</a>
                <a href="{{ route('account.appointments.index') }}" class="block text-sm">Lịch hẹn của tôi</a>
                <a href="{{ route('profile.edit') }}" class="block text-sm">Tài khoản</a>
            @else
                <a href="{{ route('login') }}" class="block text-sm">Đăng nhập</a>
                <a href="{{ route('register') }}" class="block text-sm">Đăng ký</a>
            @endauth
        </div>
    </header>

    <main class="flex-1">
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="border border-sage-600/40 bg-sage-50 text-sage-700 text-sm px-4 py-3 rounded-sm">{{ session('success') }}</div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="border border-red-200 bg-red-50 text-red-700 text-sm px-4 py-3 rounded-sm">{{ session('error') }}</div>
            </div>
        @endif
        @if ($errors->any())
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="border border-red-200 bg-red-50 text-red-700 text-sm px-4 py-3 rounded-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="bg-ink text-cream-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <div class="font-extrabold text-2xl mb-3">{{ config('app.name', 'BeautyShop') }}</div>
                <p class="text-sm text-cream-100/70 leading-relaxed">Mỹ phẩm chính hãng &amp; dịch vụ làm đẹp: gội đầu, phun xăm, nối mi, chăm sóc da — nơi vẻ đẹp hoài cổ gặp gỡ sự tươi mới hiện đại.</p>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-cream-100/50 mb-3">Mua sắm</div>
                <ul class="space-y-2 text-sm text-cream-100/80">
                    <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('products.index', ['sort' => 'newest']) }}" class="hover:text-white transition">Hàng mới về</a></li>
                    <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition">Yêu thích</a></li>
                </ul>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-cream-100/50 mb-3">Dịch vụ</div>
                <ul class="space-y-2 text-sm text-cream-100/80">
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition">Tất cả dịch vụ</a></li>
                    @auth
                        <li><a href="{{ route('account.appointments.index') }}" class="hover:text-white transition">Lịch hẹn của tôi</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <div class="text-xs uppercase tracking-widest text-cream-100/50 mb-3">Hỗ trợ</div>
                <p class="text-sm text-cream-100/80">Hotline: 1900 0000</p>
                <p class="text-sm text-cream-100/80 mt-1">08:30 – 17:30, Thứ 2 – Thứ 7</p>
            </div>
        </div>
        <div class="border-t border-white/10 py-6 text-center text-xs text-cream-100/40">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</div>
    </footer>
</div>

<style>
    @keyframes marquee {
        from { transform: translateX(0); }
        to { transform: translateX(-50%); }
    }
</style>
</body>
</html>
