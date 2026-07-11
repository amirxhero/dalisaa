<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private const CATALOG = [
        'mobile' => [
            'brands' => ['سامسونگ', 'شیائومی', 'اپل', 'آنر', 'نوکیا'],
            'models' => ['Galaxy M14', 'Redmi 12C', 'iPhone 12', 'Honor X7', 'Galaxy A23', 'POCO C55', 'Nokia G21'],
            'priceRange' => [3_500_000, 45_000_000],
            'specKeys' => ['حافظه داخلی', 'رم', 'صفحه نمایش', 'دوربین'],
        ],
        'tablet' => [
            'brands' => ['اپل', 'سامسونگ', 'شیائومی', 'لنوو'],
            'models' => ['Tab A8', 'iPad 9th Gen', 'Pad 5', 'Tab M10'],
            'priceRange' => [8_000_000, 40_000_000],
            'specKeys' => ['حافظه داخلی', 'صفحه نمایش', 'پردازنده', 'باتری'],
        ],
        'watch' => [
            'brands' => ['اپل', 'سامسونگ', 'شیائومی', 'آمازفیت', 'هواوی'],
            'models' => ['Watch GT3', 'Galaxy Fit3', 'Mi Band 8', 'Watch Series 9', 'Watch GS3'],
            'priceRange' => [1_200_000, 25_000_000],
            'specKeys' => ['صفحه نمایش', 'مقاومت آب', 'باتری', 'بدنه'],
        ],
        'headphone' => [
            'brands' => ['سونی', 'جی‌بی‌ال', 'بیتس', 'اونیکوما', 'شیائومی'],
            'models' => ['WH-CH520', 'Tune 510BT', 'Studio Buds', 'B70 Pro', 'Buds 4'],
            'priceRange' => [350_000, 9_500_000],
            'specKeys' => ['نوع اتصال', 'میکروفون', 'باتری'],
        ],
    ];

    public function definition(): array
    {
        $slugKey = fake()->randomElement(array_keys(self::CATALOG));
        $meta = self::CATALOG[$slugKey];
        $brand = fake()->randomElement($meta['brands']);
        $model = fake()->randomElement($meta['models']);
        $title = "{$this->productNoun($slugKey)} {$brand} مدل {$model}";
        $price = fake()->numberBetween(...$meta['priceRange']);
        $hasDiscount = fake()->boolean(45);
        $regularPrice = $hasDiscount ? (int) round($price * fake()->randomFloat(2, 1.05, 1.35)) : null;

        $specs = ['برند' => $brand];
        foreach ($meta['specKeys'] as $key) {
            $specs[$key] = $this->specValue($key);
        }
        $specs['گارانتی'] = fake()->randomElement(['۶ ماهه شرکتی', '۱۲ ماهه شرکتی', '۱۸ ماهه شرکتی']);

        return [
            'category_id' => Category::where('slug', $slugKey)->value('id') ?? Category::inRandomOrder()->value('id'),
            'brand' => $brand,
            'title' => $title,
            'slug' => Str::slug($title.'-'.fake()->unique()->numberBetween(1000, 999999), '-'),
            'sku' => 'SLK-'.strtoupper(Str::random(3)).'-'.fake()->unique()->numberBetween(1000, 9999),
            'description' => $this->description($slugKey, $title),
            'price' => $price,
            'regular_price' => $regularPrice,
            'stock' => fake()->numberBetween(0, 40),
            'highlights' => $this->highlights($slugKey),
            'specs' => $specs,
            'rating_cache' => 0,
            'reviews_count_cache' => 0,
            'is_active' => true,
        ];
    }

    private function productNoun(string $categorySlug): string
    {
        return match ($categorySlug) {
            'mobile' => 'گوشی موبایل',
            'tablet' => 'تبلت',
            'watch' => 'ساعت هوشمند',
            'headphone' => 'هدفون',
            default => 'کالای',
        };
    }

    private function specValue(string $key): string
    {
        return match ($key) {
            'حافظه داخلی' => fake()->randomElement(['۶۴ گیگابایت', '۱۲۸ گیگابایت', '۲۵۶ گیگابایت']),
            'رم' => fake()->randomElement(['۴ گیگابایت', '۶ گیگابایت', '۸ گیگابایت']),
            'صفحه نمایش' => fake()->randomElement(['۶.۱ اینچ AMOLED', '۶.۵ اینچ IPS LCD', '۱.۴ اینچ AMOLED', '۱۰.۴ اینچ Liquid Retina']),
            'دوربین' => fake()->randomElement(['۵۰ مگاپیکسل دوگانه', '۴۸ مگاپیکسل سه‌گانه', '۱۰۸ مگاپیکسل چهارگانه']),
            'پردازنده' => fake()->randomElement(['Snapdragon 680', 'Apple A14', 'MediaTek Helio G99']),
            'باتری' => fake()->randomElement(['تا ۱۸ ساعت با یک بار شارژ', 'تا ۳۰ ساعت با یک بار شارژ', '۵۰۰۰ میلی‌آمپر ساعت']),
            'مقاومت آب' => fake()->randomElement(['5ATM + IP68', 'تا عمق ۵۰ متر (WR50)', 'IP67']),
            'بدنه' => fake()->randomElement(['آلومینیوم سبک', 'استیل ضدزنگ', 'پلی‌کربنات مقاوم']),
            'نوع اتصال' => fake()->randomElement(['بی‌سیم بلوتوث ۵.۳', 'بی‌سیم ۲.۴ گیگاهرتز', 'سیم‌دار 3.5mm']),
            'میکروفون' => fake()->randomElement(['حذف نویز فعال', 'داخلی استاندارد', 'قابل تنظیم بوم']),
            default => fake()->word(),
        };
    }

    private function description(string $categorySlug, string $title): string
    {
        return match ($categorySlug) {
            'mobile' => "{$title} با طراحی مدرن، عملکرد روان و دوربینی قدرتمند، تجربه‌ای متفاوت از استفاده روزمره را برایتان رقم می‌زند. این محصول به صورت اورجینال و با گارانتی معتبر از فروشگاه سالیکا عرضه می‌شود.",
            'tablet' => "{$title} ترکیبی از قدرت پردازشی بالا، صفحه‌نمایش خیره‌کننده و باتری بادوام است که آن را برای کار، تحصیل و سرگرمی به انتخابی مناسب تبدیل می‌کند.",
            'watch' => "{$title} همراهی هوشمند برای سلامتی و سبک زندگی فعال شماست؛ از ردیابی دقیق ورزش‌ها گرفته تا اعلان‌های آنی و باتری بادوام.",
            'headphone' => "{$title} صدایی واضح و باکیفیت را همراه با راحتی استفاده طولانی‌مدت ارائه می‌دهد. مناسب برای بازی، تماس و گوش دادن به موسیقی.",
            default => "{$title} یکی از محصولات پرطرفدار فروشگاه سالیکا با کیفیت ساخت عالی و قیمت مناسب است.",
        };
    }

    private function highlights(string $categorySlug): array
    {
        return match ($categorySlug) {
            'mobile' => [
                'اورجینال و دارای گارانتی رسمی شرکتی',
                'دوربین حرفه‌ای برای عکاسی در هر شرایط نوری',
                'باتری پرقدرت با شارژ سریع',
                'ارسال اکسپرس و امکان پرداخت در محل',
            ],
            'watch' => [
                'ردیابی دقیق ضربان قلب، خواب و ورزش‌های روزانه',
                'مقاوم در برابر آب و ضربه برای استفاده روزمره',
                'باتری بادوام با یک بار شارژ',
                'سازگار با گوشی‌های اندروید و آیفون',
            ],
            'tablet' => [
                'پردازنده قدرتمند برای اجرای روان اپلیکیشن‌ها',
                'صفحه‌نمایش با رنگ‌های واقعی و روشنایی بالا',
                'باتری مناسب برای یک روز کامل استفاده',
                'وزن سبک و طراحی جمع‌وجور برای حمل آسان',
            ],
            'headphone' => [
                'اتصال پایدار بی‌سیم با تأخیر پایین',
                'میکروفون حذف نویز برای تماس و گیمینگ',
                'باتری بادوام برای استفاده طولانی',
                'طراحی ارگونومیک و راحت روی گوش',
            ],
            default => [
                'کیفیت ساخت عالی و مواد اولیه مرغوب',
                'گارانتی معتبر شرکتی',
                'ارسال سریع و بسته‌بندی مطمئن',
            ],
        };
    }
}
