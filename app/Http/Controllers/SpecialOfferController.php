<?php

namespace App\Http\Controllers;

use App\Models\Product;

class SpecialOfferController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->active()
            ->special()
            ->with(['category', 'variants'])
            ->latest('id')
            ->paginate(20);

        return view('special-offers.index', compact('products'));
    }
}
