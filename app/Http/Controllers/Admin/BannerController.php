<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('position');

        return view('admin.banners.index', [
            'grouped'   => $banners,
            'positions' => Banner::POSITIONS,
        ]);
    }

    public function create()
    {
        return view('admin.banners.create', ['positions' => Banner::POSITIONS]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request, true);

        $banner = Banner::create($this->fields($data));

        $this->syncImages($request, $banner);

        return redirect()->route('admin.banners.index')
            ->with('success', 'بنر با موفقیت ایجاد شد.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', [
            'banner'    => $banner,
            'positions' => Banner::POSITIONS,
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->validateData($request, false);

        $banner->update($this->fields($data));

        $this->syncImages($request, $banner);

        return redirect()->route('admin.banners.index')
            ->with('success', 'بنر با موفقیت ویرایش شد.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return back()->with('success', 'بنر حذف شد.');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return back();
    }

    private function validateData(Request $request, bool $creating): array
    {
        return $request->validate([
            'title'         => 'nullable|string|max:255',
            'position'      => ['required', Rule::in(array_keys(Banner::POSITIONS))],
            'link'          => 'nullable|url',
            'sort_order'    => 'nullable|integer|min:0',
            'is_active'     => 'nullable|boolean',
            'desktop_image' => ($creating ? 'required' : 'nullable') . '|image|max:5120',
            'mobile_image'  => 'nullable|image|max:5120',
        ]);
    }

    private function fields(array $data): array
    {
        return [
            'title'      => $data['title'] ?? null,
            'position'   => $data['position'],
            'link'       => $data['link'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active'  => (bool) ($data['is_active'] ?? false),
        ];
    }

    private function syncImages(Request $request, Banner $banner): void
    {
        if ($request->hasFile('desktop_image')) {
            $banner->clearMediaCollection('desktop');
            $banner->addMediaFromRequest('desktop_image')->toMediaCollection('desktop');
        }

        if ($request->hasFile('mobile_image')) {
            $banner->clearMediaCollection('mobile');
            $banner->addMediaFromRequest('mobile_image')->toMediaCollection('mobile');
        }
    }
}
