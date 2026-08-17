<x-shop-layout title="Yêu thích - {{ config('app.name') }}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h1 class="font-karla font-bold text-3xl text-ink mb-8">Sản phẩm yêu thích</h1>

        @if($wishlists->isEmpty())
            <div class="border border-cream-300 p-16 text-center text-ink/50">Bạn chưa lưu sản phẩm yêu thích nào.</div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-5 gap-y-10">
                @foreach($wishlists as $wishlist)
                    <x-product-card :product="$wishlist->product" />
                @endforeach
            </div>
            <div class="mt-10">{{ $wishlists->links() }}</div>
        @endif
    </div>
</x-shop-layout>
