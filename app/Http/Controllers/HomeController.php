<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceCategory;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::live()->with('product.images')->orderBy('sort_order')->orderByDesc('id')->get();
        $featuredProducts = Product::active()->featured()->with(['images', 'variants'])->latest()->take(8)->get();
        $featuredServices = Service::active()->featured()->with('category')->latest()->take(6)->get();
        $categories = Category::active()->root()->withCount('products')->take(6)->get();
        $serviceCategories = ServiceCategory::active()->withCount('services')->take(6)->get();

        return view('home', compact('banners', 'featuredProducts', 'featuredServices', 'categories', 'serviceCategories'));
    }
}
