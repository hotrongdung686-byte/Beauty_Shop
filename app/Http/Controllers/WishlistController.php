<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with(['product.images', 'product.variants'])->latest()->paginate(12);

        return view('account.wishlist', compact('wishlists'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Đã xóa khỏi danh sách yêu thích.';
        } else {
            Wishlist::create(['user_id' => Auth::id(), 'product_id' => $product->id]);
            $message = 'Đã thêm vào danh sách yêu thích.';
        }

        return back()->with('success', $message);
    }
}
