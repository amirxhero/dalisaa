<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WebpImageService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::getTree();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::getTree();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'name_en'    => 'nullable|string|max:100',
            'slug'       => 'nullable|string|max:100|unique:categories,slug',
            'parent_id'  => 'nullable|exists:categories,id',
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'image'      => 'nullable|image|max:5120',
        ]);

        $data['slug'] = $this->generateSlug($data);

        $category = Category::create($data);

        if ($request->hasFile('image')) {
            app(WebpImageService::class)->addToMediaCollection($category, $request->file('image'), 'image');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }

    public function edit(Category $category)
    {
        $parents = Category::getTree($category->id);

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'name_en'    => 'nullable|string|max:100',
            'slug'       => 'nullable|string|max:100|unique:categories,slug,'.$category->id,
            'parent_id'  => 'nullable|exists:categories,id',
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'image'      => 'nullable|image|max:5120',
        ]);

        $data['slug'] = $this->generateSlug($data, $category->id);

        if (!empty($data['parent_id'])) {
            $descendants = $category->getDescendantsIds();
            if ($data['parent_id'] == $category->id || in_array($data['parent_id'], $descendants)) {
                return back()->withErrors(['parent_id' => 'دسته والد نمی‌تواند خود یا یکی از زیرمجموعه‌های این دسته باشد.'])->withInput();
            }
        }

        $category->update($data);

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('image');
            app(WebpImageService::class)->addToMediaCollection($category, $request->file('image'), 'image');
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت ویرایش شد.');
    }

    private function generateSlug(array $data, ?int $ignoreId = null): string
    {
        if (!empty($data['slug'])) {
            $baseSlug = Str::slug($data['slug']);
        } else {
            $raw = !empty($data['name_en']) ? $data['name_en'] : $data['name'];
            $baseSlug = Str::slug($raw);
            if (empty($baseSlug)) {
                $baseSlug = 'category';
            }
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'این دسته‌بندی دارای محصول است و قابل حذف نیست.');
        }

        $category->delete();

        return back()->with('success', 'دسته‌بندی حذف شد.');
    }
}
