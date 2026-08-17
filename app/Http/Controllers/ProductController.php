<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['images', 'variants', 'brand', 'category']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categorySlug = $request->string('category')->value()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        if ($brandSlug = $request->string('brand')->value()) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brandSlug));
        }

        $sort = $request->string('sort')->value();
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('is_featured')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'variants', 'brand', 'category']);

        $reviews = Review::with('user')->forProduct($product->id)->approved()->latest()->paginate(10);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['images', 'variants'])
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'reviews', 'related'));
    }
}
