<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $services = $query->latest()->paginate(15)->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.form', [
            'service' => new Service,
            'categories' => ServiceCategory::orderBy('name')->get(),
            'allStaff' => Staff::orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service = Service::create($data);
        $service->staff()->sync($request->input('staff_ids', []));

        return redirect()->route('admin.services.edit', $service)->with('success', 'Đã tạo dịch vụ.');
    }

    public function edit(Service $service)
    {
        $service->load('staff');

        return view('admin.services.form', [
            'service' => $service,
            'categories' => ServiceCategory::orderBy('name')->get(),
            'allStaff' => Staff::orderBy('full_name')->get(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);

        if ($data['name'] !== $service->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $service->id);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $data['image'] = $request->file('image')->store('services', 'public');
        } elseif ($request->boolean('remove_image') && $service->image) {
            Storage::disk('public')->delete($service->image);
            $data['image'] = null;
        }

        $service->update($data);
        $service->staff()->sync($request->input('staff_ids', []));

        return redirect()->route('admin.services.edit', $service)->with('success', 'Đã cập nhật dịch vụ.');
    }

    public function destroy(Service $service)
    {
        if ($service->appointments()->exists()) {
            return back()->with('error', 'Không thể xóa dịch vụ đã có lịch hẹn.');
        }

        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->staff()->detach();
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Đã xóa dịch vụ.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]) + ['deposit_amount' => $request->input('deposit_amount') ?: 0];
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while (Service::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
