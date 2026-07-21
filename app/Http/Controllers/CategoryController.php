<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $category->load(['children' => fn ($q) => $q->orderBy('sort_order'), 'parent.children']);

        // If category has children, show those. If it's a child category, show its parent's children (siblings + self)
        $subcategories = $category->children->isNotEmpty()
            ? $category->children
            : ($category->parent ? $category->parent->children : collect());

        $categoryIds = $category->getAllCategoryIds();

        $products = Product::whereIn('category_id', $categoryIds)
            ->where('is_active', true)
            ->with(['category', 'variants'])
            ->latest('id')
            ->paginate(20);

        return view('category.show', compact('category', 'subcategories', 'products'));
    }
}
