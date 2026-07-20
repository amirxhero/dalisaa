<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::withCount('products')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $brands = $query->paginate(20)->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:100',
            'title_en'    => 'nullable|string|max:100',
            'slug'        => 'nullable|string|max:100|unique:brands,slug',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:5120',
        ]);

        $data['slug'] = $this->generateSlug($data);
        $data['is_active'] = $request->boolean('is_active', true);

        $brand = Brand::create($data);

        if ($request->hasFile('image')) {
            $brand->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'برند "'.$brand->title.'" با موفقیت ایجاد شد.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:100',
            'title_en'    => 'nullable|string|max:100',
            'slug'        => 'nullable|string|max:100|unique:brands,slug,'.$brand->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:5120',
        ]);

        $data['slug'] = $this->generateSlug($data, $brand->id);
        $data['is_active'] = $request->boolean('is_active');

        $brand->update($data);

        if ($request->hasFile('image')) {
            $brand->clearMediaCollection('image');
            $brand->addMediaFromRequest('image')->toMediaCollection('image');
        }

        return redirect()->route('admin.brands.index')
            ->with('success', 'برند "'.$brand->title.'" با موفقیت ویرایش شد.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->with('error', 'این برند دارای محصول متصل است و قابل حذف نیست.');
        }

        $brand->clearMediaCollection('image');
        $brand->delete();

        return back()->with('success', 'برند حذف شد.');
    }

    public function toggleActive(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);

        return back()->with('success', 'وضعیت برند تغییر کرد.');
    }

    private function generateSlug(array $data, ?int $ignoreId = null): string
    {
        if (!empty($data['slug'])) {
            $baseSlug = Str::slug($data['slug']);
        } else {
            $raw = !empty($data['title_en']) ? $data['title_en'] : $data['title'];
            $baseSlug = Str::slug($raw);
            if (empty($baseSlug)) {
                $baseSlug = 'brand';
            }
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Brand::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
