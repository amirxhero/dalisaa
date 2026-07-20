<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $categoryIds = $category->getAllCategoryIds();

        $products = Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->with(['category', 'variants'])
            ->latest('id')
            ->paginate(20);

        return view('category.show', compact('category', 'products'));
    }
}
