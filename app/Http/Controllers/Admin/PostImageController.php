<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostImageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(['image' => 'required|image|max:5120']);

        $path = $request->file('image')->store('blog-images', 'public');

        return response()->json(['url' => Storage::url($path)]);
    }
}
