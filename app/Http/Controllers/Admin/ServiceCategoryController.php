<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->orderBy('name')->paginate(20);

        return view('admin.service-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.service-categories.form', ['category' => new ServiceCategory]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('service-categories', 'public');
        }

        ServiceCategory::create($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Đã tạo danh mục dịch vụ.');
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.service-categories.form', ['category' => $serviceCategory]);
    }

    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $data = $this->validated($request);

        if ($data['name'] !== $serviceCategory->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $serviceCategory->id);
        }

        if ($request->hasFile('image')) {
            if ($serviceCategory->image) {
                Storage::disk('public')->delete($serviceCategory->image);
            }
            $data['image'] = $request->file('image')->store('service-categories', 'public');
        } elseif ($request->boolean('remove_image') && $serviceCategory->image) {
            Storage::disk('public')->delete($serviceCategory->image);
            $data['image'] = null;
        }

        $serviceCategory->update($data);

        return redirect()->route('admin.service-categories.index')->with('success', 'Đã cập nhật danh mục dịch vụ.');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        if ($serviceCategory->services()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục còn dịch vụ.');
        }

        if ($serviceCategory->image) {
            Storage::disk('public')->delete($serviceCategory->image);
        }

        $serviceCategory->delete();

        return back()->with('success', 'Đã xóa danh mục dịch vụ.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while (ServiceCategory::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
