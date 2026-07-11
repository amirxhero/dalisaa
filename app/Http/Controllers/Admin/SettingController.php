<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $rates = CurrencyService::allRates();

        return view('admin.settings.index', compact('rates'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'usd_rate'         => 'required|numeric|min:1',
            'eur_rate'         => 'required|numeric|min:1',
            'usdt_rate'        => 'required|numeric|min:1',
            'gbp_rate'         => 'required|numeric|min:1',
            'contact_address'  => 'nullable|string|max:500',
            'contact_phone'    => 'nullable|string|max:50',
            'contact_email'    => 'nullable|email|max:100',
            'social_instagram' => 'nullable|url|max:200',
            'social_telegram'  => 'nullable|url|max:200',
            'social_whatsapp'  => 'nullable|url|max:200',
        ]);

        $currencyKeys = ['usd_rate', 'eur_rate', 'usdt_rate', 'gbp_rate'];

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        // Recalculate all non-IRR product prices to reflect the new rates
        Product::whereNotIn('price_currency', ['IRR'])->each(function (Product $product) {
            $product->update([
                'price' => CurrencyService::toToman($product->price_original, $product->price_currency),
            ]);
        });

        return back()->with('success', 'تنظیمات ذخیره شد و قیمت محصولات به‌روزرسانی شدند.');
    }
}
