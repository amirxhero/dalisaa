<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Auth::user()
            ->wishlists()
            ->with(['product.category', 'product.variants'])
            ->latest()
            ->get();

        return view('panel.wishlist', ['wishlists' => $wishlists]);
    }

    public function toggle(Product $product): RedirectResponse
    {
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'محصول از علاقه‌مندی‌ها حذف شد.');
        }

        Wishlist::create(['user_id' => $user->id, 'product_id' => $product->id]);

        return back()->with('success', 'محصول به علاقه‌مندی‌ها اضافه شد.');
    }
}
