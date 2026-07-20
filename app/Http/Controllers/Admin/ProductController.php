<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%")
                         ->orWhere('title_en', 'like', "%{$search}%");
                  });
            });
        }

        if ($cat = $request->input('category')) {
            $query->where('category_id', $cat);
        }

        if ($request->input('stock') === 'low') {
            $query->where('stock', '<', 10);
        }

        $products    = $query->paginate(20)->withQueryString();
        $categories  = Category::getTree();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::getTree();
        $brands     = Brand::where('is_active', true)->orderBy('title')->get();
        $currencies = CurrencyService::currencies();
        $rates      = CurrencyService::allRates();

        return view('admin.products.create', compact('categories', 'brands', 'currencies', 'rates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:products,slug',
            'brand_id'       => 'required|exists:brands,id',
            'category_id'    => 'required|exists:categories,id',
            'description'    => 'required|string',
            'sku'            => 'nullable|string|max:50|unique:products,sku',
            'price_currency' => 'required|in:USD,EUR,USDT,GBP,IRR',
            'price_original' => 'required|numeric|min:0',
            'discount_type'  => 'nullable|in:none,percent,amount',
            'discount_value' => 'nullable|integer|min:0',
            'stock'          => 'required|integer|min:0',
            'highlights'           => 'nullable|array',
            'highlights.*.title'   => 'nullable|string|max:255',
            'highlights.*.value'   => 'nullable|string|max:500',
            'is_active'            => 'boolean',
            'images'               => 'nullable|array',
            'images.*'             => 'image|max:5120',
            'variants'             => 'nullable|array',
            'variants.*.color_name' => 'required_with:variants|string|max:100',
            'variants.*.color_hex' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'variants.*.base_price' => 'nullable|integer|min:0',
            'variants.*.discount_type' => 'nullable|in:none,percent,amount',
            'variants.*.discount_value' => 'nullable|integer|min:0',
            'variants.*.stock'     => 'required_with:variants|integer|min:0',
            'variants.*.is_default' => 'nullable',
        ]);

        $baseToman = CurrencyService::toToman(
            (float) $data['price_original'],
            $data['price_currency']
        );

        $discountType = $data['discount_type'] ?? 'none';
        $discountValue = (int) ($data['discount_value'] ?? 0);

        if ($discountType === 'percent') {
            $data['regular_price'] = $baseToman;
            $data['price'] = max(0, (int) round($baseToman * (1 - $discountValue / 100)));
        } elseif ($discountType === 'amount') {
            $data['regular_price'] = $baseToman;
            $data['price'] = max(0, $baseToman - $discountValue);
        } else {
            $data['regular_price'] = null;
            $data['price'] = $baseToman;
        }

        $data['discount_type'] = $discountType;
        $data['discount_value'] = $discountValue;

        $data['slug'] = $this->generateSlug($data);

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku();
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['highlights'] = $this->normalizeHighlights($request->input('highlights'));

        if (!empty($data['variants'])) {
            $data['stock'] = collect($data['variants'])->sum('stock');
        }

        $product = Product::create($data);

        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $v) {
                $vBase = !empty($v['base_price']) ? (int) $v['base_price'] : null;
                $vType = $v['discount_type'] ?? 'none';
                $vValue = (int) ($v['discount_value'] ?? 0);

                if ($vBase !== null) {
                    if ($vType === 'percent') {
                        $vReg = $vBase;
                        $vPrice = max(0, (int) round($vBase * (1 - $vValue / 100)));
                    } elseif ($vType === 'amount') {
                        $vReg = $vBase;
                        $vPrice = max(0, $vBase - $vValue);
                    } else {
                        $vReg = null;
                        $vPrice = $vBase;
                    }
                } else {
                    $vReg = null;
                    $vPrice = null;
                    $vType = 'none';
                    $vValue = 0;
                }

                $product->variants()->create([
                    'color_name' => $v['color_name'],
                    'color_hex' => !empty($v['color_hex']) ? $v['color_hex'] : null,
                    'price' => $vPrice,
                    'regular_price' => $vReg,
                    'discount_type' => $vType,
                    'discount_value' => $vValue,
                    'stock' => $v['stock'],
                    'is_default' => (bool) ($v['is_default'] ?? false),
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('gallery');
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'محصول "'.$product->title.'" با موفقیت ایجاد شد.');
    }

    public function show(Product $product)
    {
        return redirect()->route('admin.products.edit', $product);
    }

    public function edit(Product $product)
    {
        $categories = Category::getTree();
        $brands     = Brand::orderBy('title')->get();
        $currencies = CurrencyService::currencies();
        $rates      = CurrencyService::allRates();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'currencies', 'rates'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:products,slug,'.$product->id,
            'brand_id'       => 'required|exists:brands,id',
            'category_id'    => 'required|exists:categories,id',
            'description'    => 'required|string',
            'sku'            => 'nullable|string|max:50|unique:products,sku,'.$product->id,
            'price_currency' => 'required|in:USD,EUR,USDT,GBP,IRR',
            'price_original' => 'required|numeric|min:0',
            'discount_type'  => 'nullable|in:none,percent,amount',
            'discount_value' => 'nullable|integer|min:0',
            'stock'          => 'required|integer|min:0',
            'highlights'           => 'nullable|array',
            'highlights.*.title'   => 'nullable|string|max:255',
            'highlights.*.value'   => 'nullable|string|max:500',
            'is_active'            => 'boolean',
            'images'               => 'nullable|array',
            'images.*'             => 'image|max:5120',
            'variants'             => 'nullable|array',
            'variants.*.id'        => 'nullable|integer',
            'variants.*.color_name' => 'required_with:variants|string|max:100',
            'variants.*.color_hex' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
            'variants.*.base_price' => 'nullable|integer|min:0',
            'variants.*.discount_type' => 'nullable|in:none,percent,amount',
            'variants.*.discount_value' => 'nullable|integer|min:0',
            'variants.*.stock'     => 'required_with:variants|integer|min:0',
            'variants.*.is_default' => 'nullable',
        ]);

        $baseToman = CurrencyService::toToman(
            (float) $data['price_original'],
            $data['price_currency']
        );

        $discountType = $data['discount_type'] ?? 'none';
        $discountValue = (int) ($data['discount_value'] ?? 0);

        if ($discountType === 'percent') {
            $data['regular_price'] = $baseToman;
            $data['price'] = max(0, (int) round($baseToman * (1 - $discountValue / 100)));
        } elseif ($discountType === 'amount') {
            $data['regular_price'] = $baseToman;
            $data['price'] = max(0, $baseToman - $discountValue);
        } else {
            $data['regular_price'] = null;
            $data['price'] = $baseToman;
        }

        $data['discount_type'] = $discountType;
        $data['discount_value'] = $discountValue;

        $data['slug'] = $this->generateSlug($data, $product->id);
        if (empty($data['sku'])) {
            $data['sku'] = $product->sku ?: $this->generateSku();
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['highlights'] = $this->normalizeHighlights($request->input('highlights'));

        if (!empty($data['variants'])) {
            $data['stock'] = collect($data['variants'])->sum('stock');
        }

        $product->update($data);

        if (!empty($data['variants'])) {
            $keptIds = [];
            foreach ($data['variants'] as $v) {
                $vBase = !empty($v['base_price']) ? (int) $v['base_price'] : null;
                $vType = $v['discount_type'] ?? 'none';
                $vValue = (int) ($v['discount_value'] ?? 0);

                if ($vBase !== null) {
                    if ($vType === 'percent') {
                        $vReg = $vBase;
                        $vPrice = max(0, (int) round($vBase * (1 - $vValue / 100)));
                    } elseif ($vType === 'amount') {
                        $vReg = $vBase;
                        $vPrice = max(0, $vBase - $vValue);
                    } else {
                        $vReg = null;
                        $vPrice = $vBase;
                    }
                } else {
                    $vReg = null;
                    $vPrice = null;
                    $vType = 'none';
                    $vValue = 0;
                }

                $variantData = [
                    'color_name' => $v['color_name'],
                    'color_hex' => !empty($v['color_hex']) ? $v['color_hex'] : null,
                    'price' => $vPrice,
                    'regular_price' => $vReg,
                    'discount_type' => $vType,
                    'discount_value' => $vValue,
                    'stock' => $v['stock'],
                    'is_default' => (bool) ($v['is_default'] ?? false),
                ];

                if (!empty($v['id'])) {
                    $variant = $product->variants()->findOrFail($v['id']);
                    $variant->update($variantData);
                    $keptIds[] = $variant->id;
                } else {
                    $newVariant = $product->variants()->create($variantData);
                    $keptIds[] = $newVariant->id;
                }
            }
            $product->variants()->whereNotIn('id', $keptIds)->delete();
        } else {
            // Delete all variants if none provided (returned to flat product status)
            $product->variants()->delete();
        }

        if ($request->hasFile('images')) {
            $product->clearMediaCollection('gallery');
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('gallery');
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'محصول "'.$product->title.'" ویرایش شد.');
    }

    public function destroy(Product $product)
    {
        $product->clearMediaCollection('gallery');
        $product->delete();

        return back()->with('success', 'محصول حذف شد.');
    }

    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate([
            'slug',
            'sku',
            'rating_cache',
            'reviews_count_cache',
        ]);

        $newProduct->title = $product->title . ' (کپی)';
        if ($product->name_en) {
            $newProduct->name_en = $product->name_en . ' (Copy)';
        }

        $newProduct->slug = $this->generateSlug([
            'title'   => $newProduct->title,
            'name_en' => $newProduct->name_en,
            'slug'    => null,
        ]);

        $newProduct->sku = $this->generateSku();
        $newProduct->is_active = false;
        $newProduct->save();

        foreach ($product->variants as $variant) {
            $newVariant = $variant->replicate(['product_id']);
            $newVariant->product_id = $newProduct->id;
            $newVariant->save();
        }

        foreach ($product->getMedia('gallery') as $mediaItem) {
            $mediaItem->copy($newProduct, 'gallery');
        }

        return redirect()->route('admin.products.edit', $newProduct)
            ->with('success', 'محصول با موفقیت کپی شد. اکنون می‌توانید مشخصات آن را ویرایش کنید.');
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', 'وضعیت محصول تغییر کرد.');
    }

    private function normalizeHighlights(?array $highlights): ?array
    {
        if (empty($highlights)) {
            return null;
        }

        $normalized = collect($highlights)
            ->map(function ($item) {
                if (is_string($item)) {
                    $value = trim($item);

                    return $value !== '' ? ['title' => '', 'value' => $value] : null;
                }

                $title = trim($item['title'] ?? '');
                $value = trim($item['value'] ?? '');

                if ($title === '' && $value === '') {
                    return null;
                }

                return compact('title', 'value');
            })
            ->filter()
            ->values()
            ->all();

        return $normalized ?: null;
    }

    private function generateSlug(array $data, ?int $ignoreId = null): string
    {
        if (!empty($data['slug'])) {
            $baseSlug = Str::slug($data['slug']);
        } else {
            $raw = !empty($data['name_en']) ? $data['name_en'] : $data['title'];
            $baseSlug = Str::slug($raw);
            if (empty($baseSlug)) {
                $baseSlug = 'product';
            }
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function generateSku(): string
    {
        do {
            $sku = 'SLK-' . strtoupper(Str::random(3)) . '-' . rand(1000, 9999);
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
