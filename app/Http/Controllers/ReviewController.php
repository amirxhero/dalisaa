<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $alreadyReviewed = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'شما قبلاً برای این محصول نظر ثبت کرده‌اید.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ], [], ['rating' => 'امتیاز', 'comment' => 'متن نظر']);

        Review::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'is_approved' => true,
        ]);

        $approved = $product->reviews()->get();
        $product->forceFill([
            'rating_cache' => $approved->isNotEmpty() ? round($approved->avg('rating'), 1) : 0,
            'reviews_count_cache' => $approved->count(),
        ])->save();

        return back()->with('success', 'نظر شما با موفقیت ثبت شد.');
    }
}
