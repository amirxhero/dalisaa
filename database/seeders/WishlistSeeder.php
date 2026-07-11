<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $productIds = Product::pluck('id')->all();

        User::where('is_admin', false)->get()->each(function (User $user) use ($productIds) {
            $picks = collect($productIds)->shuffle()->take(random_int(0, 6));

            foreach ($picks as $productId) {
                Wishlist::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
            }
        });
    }
}
