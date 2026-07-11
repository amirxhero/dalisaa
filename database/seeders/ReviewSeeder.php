<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::where('is_admin', false)->pluck('id')->all();

        if (empty($userIds)) {
            return;
        }

        Product::all()->each(function (Product $product) use ($userIds) {
            $reviewerIds = collect($userIds)->shuffle()->take(random_int(3, min(9, count($userIds))));

            foreach ($reviewerIds as $userId) {
                Review::factory()->create([
                    'product_id' => $product->id,
                    'user_id' => $userId,
                ]);
            }

            $this->refreshRatingCache($product);
        });
    }

    private function refreshRatingCache(Product $product): void
    {
        $approved = $product->reviews()->get();

        $product->forceFill([
            'rating_cache' => $approved->isNotEmpty() ? round($approved->avg('rating'), 1) : 0,
            'reviews_count_cache' => $approved->count(),
        ])->save();
    }
}
