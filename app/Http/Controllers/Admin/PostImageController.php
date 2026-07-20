<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WebpImageService;
use Illuminate\Http\Request;

class PostImageController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate(['image' => 'required|image|max:5120']);

        $path = app(WebpImageService::class)->storePublic($request->file('image'), 'blog-images');

        return response()->json(['url' => asset('storage/'.$path)]);
    }
}
