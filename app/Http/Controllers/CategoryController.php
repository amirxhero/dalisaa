<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $products = $category->products()
            ->where('is_active', true)
            ->with(['category', 'variants'])
            ->latest('id')
            ->paginate(20);

        return view('category.show', compact('category', 'products'));
    }
}
