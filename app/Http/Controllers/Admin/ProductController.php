<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'variants']);

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Đã tạo sản phẩm. Thêm phân loại (SKU) và ảnh bên dưới.');
    }

    public function edit(Product $product)
    {
        $product->load(['variants', 'images']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product)
    {
        if ($product->variants()->whereHas('orderItems')->exists()) {
            return back()->with('error', 'Không thể xóa sản phẩm đã có đơn hàng.');
        }

        $product->images->each(fn (ProductImage $img) => Storage::disk('public')->delete($img->path));
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm.');
    }

    public function storeVariant(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:80', 'unique:product_variants,sku'],
            'attribute' => ['nullable', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $variant = $product->variants()->create($data);

        if ($data['stock_quantity'] > 0) {
            InventoryMovement::create([
                'variant_id' => $variant->id,
                'type' => InventoryMovement::TYPE_IMPORT,
                'quantity' => $data['stock_quantity'],
                'note' => 'Nhập kho ban đầu',
                'created_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Đã thêm phân loại.');
    }

    public function updateVariant(Request $request, Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        $data = $request->validate([
            'attribute' => ['nullable', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $diff = $data['stock_quantity'] - $variant->stock_quantity;
        $variant->update($data);

        if ($diff !== 0) {
            InventoryMovement::create([
                'variant_id' => $variant->id,
                'type' => InventoryMovement::TYPE_ADJUST,
                'quantity' => $diff,
                'note' => 'Điều chỉnh tồn kho bởi quản trị viên',
                'created_by' => Auth::id(),
            ]);
        }

        return back()->with('success', 'Đã cập nhật phân loại.');
    }

    public function destroyVariant(Product $product, ProductVariant $variant)
    {
        abort_unless($variant->product_id === $product->id, 404);

        if ($variant->orderItems()->exists()) {
            return back()->with('error', 'Không thể xóa phân loại đã có trong đơn hàng.');
        }

        $variant->delete();

        return back()->with('success', 'Đã xóa phân loại.');
    }

    public function storeImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $path = $request->file('image')->store('products', 'public');

        $product->images()->create([
            'path' => $path,
            'is_primary' => ! $product->images()->exists(),
            'sort_order' => $product->images()->count(),
        ]);

        return back()->with('success', 'Đã tải ảnh lên.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh.');
    }

    public function setPrimaryImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Đã đặt làm ảnh đại diện.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:200'],
            'short_desc' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    protected function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $i = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
