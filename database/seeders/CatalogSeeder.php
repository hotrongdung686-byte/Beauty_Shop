<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Chăm sóc da', 'Trang điểm', 'Chăm sóc tóc', 'Nước hoa & Body',
        ])->mapWithKeys(function (string $name) {
            $category = Category::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );

            return [$name => $category];
        });

        $brands = collect([
            'La Roche-Posay', 'The Ordinary', 'Innisfree', 'Cocoon',
        ])->mapWithKeys(function (string $name) {
            $brand = Brand::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );

            return [$name => $brand];
        });

        $products = [
            [
                'name' => 'Sữa rửa mặt La Roche-Posay',
                'category' => 'Chăm sóc da',
                'brand' => 'La Roche-Posay',
                'short_desc' => 'Sữa rửa mặt dịu nhẹ cho da nhạy cảm',
                'base_price' => 350000,
                'is_featured' => true,
                'variants' => [
                    ['sku' => 'LRP-CLEAN-50', 'attribute' => '50ml', 'price' => 220000, 'stock_quantity' => 40],
                    ['sku' => 'LRP-CLEAN-100', 'attribute' => '100ml', 'price' => 350000, 'stock_quantity' => 25],
                ],
            ],
            [
                'name' => 'Serum The Ordinary Niacinamide 10%',
                'category' => 'Chăm sóc da',
                'brand' => 'The Ordinary',
                'short_desc' => 'Giảm nhờn, se khít lỗ chân lông',
                'base_price' => 280000,
                'is_featured' => true,
                'variants' => [
                    ['sku' => 'TO-NIA-30', 'attribute' => '30ml', 'price' => 280000, 'stock_quantity' => 60],
                ],
            ],
            [
                'name' => 'Dầu gội Innisfree',
                'category' => 'Chăm sóc tóc',
                'brand' => 'Innisfree',
                'short_desc' => 'Dầu gội thảo mộc phục hồi tóc hư tổn',
                'base_price' => 210000,
                'is_featured' => false,
                'variants' => [
                    ['sku' => 'INF-SHP-300', 'attribute' => '300ml', 'price' => 210000, 'stock_quantity' => 30],
                ],
            ],
            [
                'name' => 'Kem chống nắng Cocoon',
                'category' => 'Chăm sóc da',
                'brand' => 'Cocoon',
                'short_desc' => 'Chống nắng phổ rộng SPF50+ PA++++',
                'base_price' => 165000,
                'is_featured' => true,
                'variants' => [
                    ['sku' => 'COC-SUN-30', 'attribute' => '30ml', 'price' => 165000, 'stock_quantity' => 50],
                ],
            ],
            [
                'name' => 'Son kem lì Merzy',
                'category' => 'Trang điểm',
                'brand' => 'Innisfree',
                'short_desc' => 'Bảng màu trẻ trung, bám màu lâu trôi',
                'base_price' => 195000,
                'is_featured' => false,
                'variants' => [
                    ['sku' => 'MERZY-LIP-01', 'attribute' => 'Đỏ đất', 'price' => 195000, 'stock_quantity' => 35],
                    ['sku' => 'MERZY-LIP-02', 'attribute' => 'Hồng cam', 'price' => 195000, 'stock_quantity' => 35],
                ],
            ],
        ];

        foreach ($products as $data) {
            // Keyed by name (not slug) — the shop's existing catalog may use
            // hand-picked short slugs that don't match Str::slug($name).
            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                [
                    'slug' => Str::slug($data['name']),
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brands[$data['brand']]->id,
                    'short_desc' => $data['short_desc'],
                    'description' => $data['short_desc'],
                    'base_price' => $data['base_price'],
                    'is_active' => true,
                    'is_featured' => $data['is_featured'],
                ]
            );

            foreach ($data['variants'] as $variant) {
                ProductVariant::firstOrCreate(
                    ['sku' => $variant['sku']],
                    array_merge($variant, ['product_id' => $product->id])
                );
            }
        }

        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
                'type' => 'percent',
                'value' => 10,
                'min_order' => 200000,
                'max_discount' => 100000,
                'usage_limit' => 500,
                'used_count' => 0,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'is_active' => true,
            ]
        );
    }
}
