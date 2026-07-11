<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    private const COMMENTS = [
        5 => [
            'کیفیت ساخت فوق‌العاده‌ای داره، کاملا راضی‌ام.',
            'دقیقا همون چیزی بود که توی عکس‌ها دیدم، ارسال هم خیلی سریع بود.',
            'پیشنهاد می‌کنم بخرید، ارزش قیمتش رو داره.',
            'بسته‌بندی سالم و محصول اورجینال بود، ممنون از سالیکا.',
        ],
        4 => [
            'کلا خوبه ولی جای بهبود داره، در کل راضی‌ام.',
            'نسبت به قیمتش عملکرد خوبی داره.',
            'کیفیت خوب بود، فقط ارسال کمی طول کشید.',
        ],
        3 => [
            'در حد انتظار بود، نه خیلی عالی نه بد.',
            'قابل قبوله ولی توقع بیشتری داشتم.',
        ],
        2 => [
            'کیفیت زیاد راضی‌کننده نبود.',
            'با توضیحات محصول یکم تفاوت داشت.',
        ],
        1 => [
            'متاسفانه از خریدم راضی نبودم.',
        ],
    ];

    public function definition(): array
    {
        $rating = fake()->randomElement([5, 5, 5, 4, 4, 4, 3, 3, 2, 1]);

        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'rating' => $rating,
            'comment' => fake()->randomElement(self::COMMENTS[$rating]),
            'is_approved' => true,
        ];
    }
}
