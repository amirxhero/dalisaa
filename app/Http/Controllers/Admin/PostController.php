<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\JalaliDate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->latest()->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'excerpt'      => 'nullable|string|max:600',
            'content'      => 'required|string',
            'status'       => 'required|in:draft,published',
            'published_at' => ['nullable', 'regex:/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/'],
            'cover'        => 'nullable|image|max:4096',
        ], [
            'published_at.regex' => 'تاریخ انتشار معتبر نیست.',
        ]);

        $data['published_at'] = JalaliDate::toGregorian($data['published_at'] ?? null);
        $data['slug']         = $this->uniqueSlug($data['slug'] ?: $data['title']);
        $data['user_id']      = auth()->id();

        $post = Post::create($data);

        if ($request->hasFile('cover')) {
            $post->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'مقاله با موفقیت ایجاد شد.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'excerpt'      => 'nullable|string|max:600',
            'content'      => 'required|string',
            'status'       => 'required|in:draft,published',
            'published_at' => ['nullable', 'regex:/^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/'],
            'cover'        => 'nullable|image|max:4096',
        ], [
            'published_at.regex' => 'تاریخ انتشار معتبر نیست.',
        ]);

        $data['published_at'] = JalaliDate::toGregorian($data['published_at'] ?? null);

        $newSlug = Str::slug($data['slug'] ?: $data['title']);
        if ($newSlug !== $post->slug) {
            $newSlug = $this->uniqueSlug($newSlug, $post->id);
        }
        $data['slug'] = $newSlug;

        $post->update($data);

        if ($request->hasFile('cover')) {
            $post->clearMediaCollection('cover');
            $post->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'مقاله با موفقیت ویرایش شد.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return back()->with('success', 'مقاله حذف شد.');
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug  = Str::slug($base);
        $query = Post::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $i = 1;
        $candidate = $slug;
        while ($query->clone()->where('slug', $candidate)->exists()) {
            $candidate = $slug . '-' . $i++;
        }

        return $candidate;
    }
}
