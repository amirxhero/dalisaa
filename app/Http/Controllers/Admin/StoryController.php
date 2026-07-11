<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['is_active'] = $request->boolean('is_active', true);

        $story = Story::create($data);

        foreach ($request->file('images', []) as $image) {
            $story->addMedia($image)->toMediaCollection('slides');
        }

        return redirect()->route('admin.stories.index')
            ->with('success', 'استوری "'.$story->title.'" با موفقیت ایجاد شد.');
    }

    public function edit(Story $story)
    {
        return view('admin.stories.edit', compact('story'));
    }

    public function update(Request $request, Story $story)
    {
        $data = $this->validateData($request);

        $data['is_active'] = $request->boolean('is_active');

        $story->update($data);

        if ($request->hasFile('images')) {
            $story->clearMediaCollection('slides');
            foreach ($request->file('images') as $image) {
                $story->addMedia($image)->toMediaCollection('slides');
            }
        }

        return redirect()->route('admin.stories.index')
            ->with('success', 'استوری "'.$story->title.'" ویرایش شد.');
    }

    public function destroy(Story $story)
    {
        $story->clearMediaCollection('slides');
        $story->delete();

        return back()->with('success', 'استوری حذف شد.');
    }

    public function toggleActive(Story $story)
    {
        $story->update(['is_active' => ! $story->is_active]);

        return back()->with('success', 'وضعیت استوری تغییر کرد.');
    }

    private function validateData(Request $request): array
    {
        $rules = [
            'title'      => 'required|string|max:50',
            'badge'      => 'nullable|string|max:20',
            'link'       => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'boolean',
            'images'     => 'nullable|array',
            'images.*'   => 'image|max:5120',
        ];

        if ($request->route('story') === null) {
            $rules['images'] = 'required|array|min:1';
        }

        return $request->validate($rules);
    }
}
