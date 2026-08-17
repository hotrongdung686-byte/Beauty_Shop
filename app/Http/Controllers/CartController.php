<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        $items = $this->cart->items();
        $subtotal = $this->cart->subtotal();

        return view('cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $variant = ProductVariant::findOrFail($data['variant_id']);

        if (! $variant->inStock()) {
            return back()->with('error', 'Sản phẩm này đã hết hàng.');
        }

        $this->cart->add($variant->id, $data['quantity'] ?? 1);

        return back()->with('success', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update($variant->id, $data['quantity']);

        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function remove(ProductVariant $variant)
    {
        $this->cart->remove($variant->id);

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
}
