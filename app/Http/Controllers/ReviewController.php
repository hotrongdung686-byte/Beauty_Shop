<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function storeProduct(Request $request, Product $product)
    {
        $data = $this->validateReview($request);

        Review::create([
            'user_id' => Auth::id(),
            'reviewable_type' => Review::TYPE_PRODUCT,
            'reviewable_id' => $product->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'is_approved' => true,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }

    public function storeService(Request $request, Service $service)
    {
        $data = $this->validateReview($request);

        Review::create([
            'user_id' => Auth::id(),
            'reviewable_type' => Review::TYPE_SERVICE,
            'reviewable_id' => $service->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'is_approved' => true,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá dịch vụ!');
    }

    protected function validateReview(Request $request): array
    {
        return $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
