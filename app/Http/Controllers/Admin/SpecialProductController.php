<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SpecialProductController extends Controller
{
    public function index(Request $request)
    {
        $specialProducts = Product::with(['category'])
            ->where('is_special', true)
            ->latest()
            ->get();

        $search = $request->input('search');

        $searchResults = Product::with(['category'])
            ->where('is_special', false)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('brand', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(20)
            ->get();

        $categories = Category::orderBy('sort_order')->get();

        return view('admin.special-products.index', compact('specialProducts', 'searchResults', 'search', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        Product::where('id', $request->product_id)->update(['is_special' => true]);

        return back()->with('success', 'محصول به لیست شگفت‌انگیز اضافه شد.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_special' => false]);

        return back()->with('success', 'محصول از لیست شگفت‌انگیز حذف شد.');
    }
}
