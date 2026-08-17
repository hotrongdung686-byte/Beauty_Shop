<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

/**
 * Session-backed shopping cart. Storage shape: ['variant_id' => quantity].
 */
class CartService
{
    protected const SESSION_KEY = 'cart';

    public function add(int $variantId, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$variantId] = min(($cart[$variantId] ?? 0) + $quantity, 99);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->raw();

        if ($quantity <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = min($quantity, 99);
        }

        Session::put(self::SESSION_KEY, $cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        Session::put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    protected function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Cart lines with live product/variant data attached, and quantity
     * clamped down to available stock.
     */
    public function items(): Collection
    {
        $cart = $this->raw();

        if (empty($cart)) {
            return collect();
        }

        return ProductVariant::with(['product.images'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->map(function (ProductVariant $variant) use ($cart) {
                $qty = min($cart[$variant->id], max($variant->stock_quantity, 0)) ?: $cart[$variant->id];

                return [
                    'variant' => $variant,
                    'quantity' => $qty,
                    'line_total' => bcmul((string) $variant->price, (string) $qty, 2),
                ];
            });
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    public function isEmpty(): bool
    {
        return empty($this->raw());
    }
}
