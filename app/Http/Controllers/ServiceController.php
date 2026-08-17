<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::active()->with('category');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categorySlug = $request->string('category')->value()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }

        $services = $query->orderByDesc('is_featured')->orderBy('name')->paginate(12)->withQueryString();
        $categories = ServiceCategory::active()->orderBy('name')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        abort_unless($service->is_active, 404);

        $service->load(['category', 'staff' => fn ($q) => $q->active()]);

        $reviews = Review::with('user')->forService($service->id)->approved()->latest()->paginate(10);

        return view('services.show', compact('service', 'reviews'));
    }
}
