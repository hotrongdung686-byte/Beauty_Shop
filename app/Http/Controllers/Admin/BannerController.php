<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::with('product')->orderBy('sort_order')->orderByDesc('id')->paginate(20);

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', [
            'banner' => new Banner,
            'products' => Product::active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã tạo banner.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', [
            'banner' => $banner,
            'products' => Product::active()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $request->file('image')->store('banners', 'public');
        } elseif ($request->boolean('remove_image') && $banner->image) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = null;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật banner.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'product_id' => ['nullable', 'exists:products,id'],
            'custom_url' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'background_color' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]) + [
            'button_text' => $request->input('button_text') ?: 'Mua ngay',
            'sort_order' => $request->input('sort_order') ?: 0,
        ];
    }
}
