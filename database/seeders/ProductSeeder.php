<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductSeeder extends Seeder
{
    private const CDN = 'https://kaveh.moeinwp.com/1/wp-content/uploads/2022/11/';

    private const CATEGORY_SLUGS = [
        'موبایل' => 'mobile',
        'ساعت' => 'watch',
        'تبلت' => 'tablet',
        'هدفون' => 'headphone',
    ];

    public function run(): void
    {
        foreach ($this->catalog() as $data) {
            $this->createProduct($data);
        }

        // Extra synthetic products for a richer, paginated catalog.
        Product::factory()
            ->count(24)
            ->create()
            ->each(fn (Product $product) => $this->attachPlaceholderImage($product));
    }

    private function createProduct(array $data): void
    {
        $categorySlug = self::CATEGORY_SLUGS[$data['category']];
        $category = Category::where('slug', $categorySlug)->first();

        $specs = array_merge(
            ['برند' => $data['brand']],
            $data['specs'],
            ['گارانتی' => $data['category'] === 'هدفون' ? '۶ ماهه شرکتی' : ($data['category'] === 'ساعت' ? '۱۲ ماهه شرکتی' : '۱۸ ماهه شرکتی')],
        );

        $product = Product::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'category_id' => $category->id,
                'brand' => $data['brand'],
                'title' => $data['title'],
                'sku' => $data['sku'],
                'description' => $data['description'],
                'price' => $data['price'],
                'regular_price' => $data['regularPrice'] ?? null,
                'stock' => $data['stock'],
                'highlights' => $data['highlights'],
                'specs' => $specs,
                'is_active' => true,
            ]
        );

        if (!empty($data['hasVariants']) && !empty($data['colorNames'])) {
            foreach ($data['colorNames'] as $i => $colorName) {
                $product->variants()->updateOrCreate(
                    ['color_name' => $colorName],
                    [
                        'color_hex' => $data['swatches'][$i] ?? '#000000',
                        'stock' => (int) round($data['stock'] / max(1, count($data['colorNames']))),
                        'is_default' => $i === 0,
                    ]
                );
            }
        }

        if ($product->getMedia('gallery')->isEmpty()) {
            $this->attachImage($product, $data['image']);
        }
    }

    private function attachImage(Product $product, string $url): void
    {
        try {
            $product->addMediaFromUrl($url)->toMediaCollection('gallery');
        } catch (Throwable $e) {
            Log::warning("Could not fetch product image for [{$product->slug}]: {$e->getMessage()}");
        }
    }

    private function attachPlaceholderImage(Product $product): void
    {
        $seed = $product->id + 100;
        $this->attachImage($product, "https://picsum.photos/seed/salika{$seed}/800/800");
    }

    private function catalog(): array
    {
        return [
            [
                'slug' => 'apple-watch-ultra-49',
                'title' => 'ساعت هوشمند اپل واچ مدل Ultra 49 میلی‌متری',
                'brand' => 'اپل',
                'category' => 'ساعت',
                'image' => self::CDN.'b0af2ec78668c85506c1edc260b42ff447f019c8_166720188545038_nobg.png',
                'price' => 407800,
                'regularPrice' => 457800,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#c8a25c', '#e5e5e5'],
                'colorNames' => ['تیتانیوم مشکی', 'طلایی', 'نقره‌ای'],
                'sku' => 'SLK-WA-4901',
                'stock' => 14,
                'specs' => [
                    'مدل' => 'Apple Watch Ultra 49mm',
                    'صفحه نمایش' => '۱.۹۲ اینچ Always-On Retina',
                    'مقاومت آب' => 'تا عمق ۱۰۰ متر (WR100)',
                    'باتری' => 'تا ۳۶ ساعت با یک بار شارژ',
                    'بدنه' => 'تیتانیوم درجه ۵',
                ],
                'description' => $this->describe('ساعت', 'ساعت هوشمند اپل واچ مدل Ultra 49 میلی‌متری'),
                'highlights' => $this->highlights('ساعت'),
            ],
            [
                'slug' => 'samsung-galaxy-watch5-44',
                'title' => 'ساعت هوشمند سامسونگ مدل Galaxy Watch5 44mm',
                'brand' => 'سامسونگ',
                'category' => 'ساعت',
                'image' => self::CDN.'760c0407a514c473eb40542cc8b3d5ee988f1ca2_166126382925658_nobg.png',
                'price' => 6599000,
                'regularPrice' => 6999000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#8a8f99'],
                'colorNames' => ['گرافیتی', 'نقره‌ای'],
                'sku' => 'SLK-WA-4402',
                'stock' => 20,
                'specs' => [
                    'مدل' => 'Galaxy Watch5 44mm',
                    'صفحه نمایش' => '۱.۴ اینچ Super AMOLED',
                    'مقاومت آب' => '5ATM + IP68',
                    'باتری' => 'تا ۵۰ ساعت با یک بار شارژ',
                    'بدنه' => 'آلومینیوم سبک',
                ],
                'description' => $this->describe('ساعت', 'ساعت هوشمند سامسونگ مدل Galaxy Watch5 44mm'),
                'highlights' => $this->highlights('ساعت'),
            ],
            [
                'slug' => 'apple-watch-series7-45',
                'title' => 'ساعت هوشمند اپل واچ سری ۷ مدل ۴۵mm',
                'brand' => 'اپل',
                'category' => 'ساعت',
                'image' => self::CDN.'7b190e819c384b0f38a4158dae481afb33d3a511_165821238417159_nobg.png',
                'price' => 4990000,
                'regularPrice' => 5400000,
                'sku' => 'SLK-WA-4503',
                'stock' => 9,
                'specs' => [
                    'مدل' => 'Apple Watch Series 7 45mm',
                    'صفحه نمایش' => '۱.۷۵ اینچ Always-On Retina',
                    'مقاومت آب' => 'تا عمق ۵۰ متر (WR50)',
                    'باتری' => 'تا ۱۸ ساعت با یک بار شارژ',
                ],
                'description' => $this->describe('ساعت', 'ساعت هوشمند اپل واچ سری ۷ مدل ۴۵mm'),
                'highlights' => $this->highlights('ساعت'),
            ],
            [
                'slug' => 'apple-watch-se-2021',
                'title' => 'ساعت هوشمند اپل واچ سری SE مدل ۲۰۲۱',
                'brand' => 'اپل',
                'category' => 'ساعت',
                'image' => self::CDN.'e7b574a8c663fe577391b6f88c182a947ad78441_165735928199955_nobg-e1744653734759.png',
                'price' => 1200000,
                'sku' => 'SLK-WA-4104',
                'stock' => 5,
                'specs' => [
                    'مدل' => 'Apple Watch SE 2021',
                    'صفحه نمایش' => '۱.۵۷ اینچ Retina',
                    'مقاومت آب' => 'تا عمق ۵۰ متر (WR50)',
                    'باتری' => 'تا ۱۸ ساعت با یک بار شارژ',
                ],
                'description' => $this->describe('ساعت', 'ساعت هوشمند اپل واچ سری SE مدل ۲۰۲۱'),
                'highlights' => $this->highlights('ساعت'),
            ],
            [
                'slug' => 'onikuma-b60-headset',
                'title' => 'هدست مخصوص بازی بی‌سیم اونیکوما مدل B60',
                'brand' => 'اونیکوما',
                'category' => 'هدفون',
                'image' => self::CDN.'b353d99cbafce342bec8fc23ca4e75c21b02fc9c_164482380448555_nobg.png',
                'price' => 499000,
                'regularPrice' => 699000,
                'sku' => 'SLK-HD-2201',
                'stock' => 30,
                'specs' => [
                    'مدل' => 'Onikuma B60',
                    'نوع اتصال' => 'بی‌سیم ۲.۴ گیگاهرتز / بلوتوث',
                    'میکروفون' => 'حذف نویز، قابل تنظیم',
                    'باتری' => 'تا ۱۲ ساعت پخش پیوسته',
                ],
                'description' => $this->describe('هدفون', 'هدست مخصوص بازی بی‌سیم اونیکوما مدل B60'),
                'highlights' => $this->highlights('هدفون'),
            ],
            [
                'slug' => 'ipad-air-5-silver',
                'title' => 'تبلت اپل مدل iPad Air 5th Generation Wi-Fi - نقره‌ای',
                'brand' => 'اپل',
                'category' => 'تبلت',
                'image' => self::CDN.'858dd307e10ddbe07f9d0c7b1b8885dd60cd4c28_165357188464467_nobg.png',
                'price' => 25780000,
                'regularPrice' => 27780000,
                'hasVariants' => true,
                'swatches' => ['#e4e4e4', '#1b1b1b'],
                'colorNames' => ['نقره‌ای', 'خاکستری فضایی'],
                'sku' => 'SLK-TB-3301',
                'stock' => 11,
                'specs' => [
                    'مدل' => 'iPad Air 5th Generation Wi-Fi',
                    'حافظه داخلی' => '۶۴ گیگابایت',
                    'صفحه نمایش' => '۱۰.۹ اینچ Liquid Retina',
                    'پردازنده' => 'Apple M1',
                    'باتری' => 'تا ۱۰ ساعت استفاده مداوم',
                ],
                'description' => $this->describe('تبلت', 'تبلت اپل مدل iPad Air 5th Generation Wi-Fi - نقره‌ای'),
                'highlights' => $this->highlights('تبلت'),
            ],
            [
                'slug' => 'ipad-air-5-space-gray',
                'title' => 'تبلت اپل مدل iPad Air 5th Generation Wi-Fi - خاکستری فضایی',
                'brand' => 'اپل',
                'category' => 'تبلت',
                'image' => self::CDN.'7a549ce3bf1310e3989018793b7019371dd49b64_164974485859619_nobg.png',
                'price' => 19000000,
                'regularPrice' => 21770000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#e4e4e4'],
                'colorNames' => ['خاکستری فضایی', 'نقره‌ای'],
                'sku' => 'SLK-TB-3302',
                'stock' => 6,
                'specs' => [
                    'مدل' => 'iPad Air 5th Generation Wi-Fi',
                    'حافظه داخلی' => '۲۵۶ گیگابایت',
                    'صفحه نمایش' => '۱۰.۹ اینچ Liquid Retina',
                    'پردازنده' => 'Apple M1',
                    'باتری' => 'تا ۱۰ ساعت استفاده مداوم',
                ],
                'description' => $this->describe('تبلت', 'تبلت اپل مدل iPad Air 5th Generation Wi-Fi - خاکستری فضایی'),
                'highlights' => $this->highlights('تبلت'),
            ],
            [
                'slug' => 'honor-50-lite',
                'title' => 'گوشی موبایل آنر مدل 50 Lite ظرفیت ۱۲۸ گیگابایت',
                'brand' => 'آنر',
                'category' => 'موبایل',
                'image' => self::CDN.'94120dd17a3e09aaf9ff9c0d7365f7c4cb0fc339_166313785268031_nobg.png',
                'price' => 29900000,
                'regularPrice' => 39900000,
                'sku' => 'SLK-MB-1101',
                'stock' => 4,
                'specs' => [
                    'مدل' => 'Honor 50 Lite',
                    'حافظه داخلی' => '۱۲۸ گیگابایت',
                    'رم' => '۶ گیگابایت',
                    'صفحه نمایش' => '۶.۶۷ اینچ AMOLED',
                    'دوربین' => '۴۸ مگاپیکسل چهارگانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل آنر مدل 50 Lite ظرفیت ۱۲۸ گیگابایت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'xiaomi-poco-x4-pro',
                'title' => 'گوشی موبایل شیائومی مدل POCO X4 Pro دو سیم‌کارت',
                'brand' => 'شیائومی',
                'category' => 'موبایل',
                'image' => self::CDN.'932752a3594b8f5d1ddfa62fe4d2a29824096916_1656405344-322232_nobg.png',
                'price' => 8999000,
                'regularPrice' => 9099000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#f2c9c9', '#3a4f6b'],
                'colorNames' => ['مشکی', 'صورتی', 'آبی'],
                'sku' => 'SLK-MB-1102',
                'stock' => 17,
                'specs' => [
                    'مدل' => 'POCO X4 Pro 5G',
                    'حافظه داخلی' => '۱۲۸ گیگابایت',
                    'رم' => '۶ گیگابایت',
                    'صفحه نمایش' => '۶.۶۷ اینچ AMOLED 120Hz',
                    'دوربین' => '۱۰۸ مگاپیکسل سه‌گانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل شیائومی مدل POCO X4 Pro دو سیم‌کارت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'samsung-galaxy-a13',
                'title' => 'گوشی موبایل سامسونگ مدل Galaxy A13 دو سیم‌کارت',
                'brand' => 'سامسونگ',
                'category' => 'موبایل',
                'image' => self::CDN.'3b80e5838f5ff4f674f82d5615296c06cd4f9f8c_1656404956-131084_nobg.png',
                'price' => 4389000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#3a4f6b'],
                'colorNames' => ['مشکی', 'آبی'],
                'sku' => 'SLK-MB-1103',
                'stock' => 25,
                'specs' => [
                    'مدل' => 'Galaxy A13 SM-A137F/DS',
                    'حافظه داخلی' => '۶۴ گیگابایت',
                    'رم' => '۴ گیگابایت',
                    'صفحه نمایش' => '۶.۶ اینچ PLS LCD',
                    'دوربین' => '۵۰ مگاپیکسل چهارگانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل سامسونگ مدل Galaxy A13 دو سیم‌کارت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'xiaomi-redmi-note11',
                'title' => 'گوشی موبایل شیائومی مدل Redmi Note 11 دو سیم‌کارت',
                'brand' => 'شیائومی',
                'category' => 'موبایل',
                'image' => self::CDN.'2a9b44d5b63353967db5ccd98659ba8f07bfe8de_1647176377-199719_nobg.png',
                'price' => 5580000,
                'sku' => 'SLK-MB-1104',
                'stock' => 19,
                'specs' => [
                    'مدل' => 'Redmi Note 11',
                    'حافظه داخلی' => '۶۴ گیگابایت',
                    'رم' => '۴ گیگابایت',
                    'صفحه نمایش' => '۶.۴۳ اینچ AMOLED',
                    'دوربین' => '۵۰ مگاپیکسل چهارگانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل شیائومی مدل Redmi Note 11 دو سیم‌کارت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'samsung-galaxy-a32',
                'title' => 'گوشی موبایل سامسونگ مدل Galaxy A32 دو سیم‌کارت',
                'brand' => 'سامسونگ',
                'category' => 'موبایل',
                'image' => self::CDN.'3b80e5838f5ff4f674f82d5615296c06cd4f9f8c_1656404956-131084_nobg.png',
                'price' => 5991000,
                'regularPrice' => 6113265,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#e5e5e5'],
                'colorNames' => ['مشکی', 'نقره‌ای'],
                'sku' => 'SLK-MB-1105',
                'stock' => 13,
                'specs' => [
                    'مدل' => 'Galaxy A32 SM-A325F/DS',
                    'حافظه داخلی' => '۱۲۸ گیگابایت',
                    'رم' => '۶ گیگابایت',
                    'صفحه نمایش' => '۶.۴ اینچ Super AMOLED',
                    'دوربین' => '۶۴ مگاپیکسل چهارگانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل سامسونگ مدل Galaxy A32 دو سیم‌کارت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'xiaomi-poco-m4-pro',
                'title' => 'گوشی موبایل شیائومی مدل POCO M4 Pro',
                'brand' => 'شیائومی',
                'category' => 'موبایل',
                'image' => self::CDN.'2a9b44d5b63353967db5ccd98659ba8f07bfe8de_1647176377-199719_nobg.png',
                'price' => 6990000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b'],
                'colorNames' => ['مشکی'],
                'sku' => 'SLK-MB-1106',
                'stock' => 22,
                'specs' => [
                    'مدل' => 'POCO M4 Pro 2201117PG',
                    'حافظه داخلی' => '۶۴ گیگابایت',
                    'رم' => '۴ گیگابایت',
                    'صفحه نمایش' => '۶.۶ اینچ AMOLED 90Hz',
                    'دوربین' => '۵۰ مگاپیکسل سه‌گانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل شیائومی مدل POCO M4 Pro'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'iphone-13-ch',
                'title' => 'گوشی موبایل اپل مدل iPhone 13 CH دو سیم‌کارت',
                'brand' => 'اپل',
                'category' => 'موبایل',
                'image' => self::CDN.'33cc19680d37b40f3030752c36978e0d76ea980b_1656430769-144213_nobg.png',
                'price' => 33279000,
                'hasVariants' => true,
                'swatches' => ['#1b1b1b', '#f2c9c9'],
                'colorNames' => ['مشکی', 'صورتی'],
                'sku' => 'SLK-MB-1107',
                'stock' => 8,
                'specs' => [
                    'مدل' => 'iPhone 13 CH',
                    'حافظه داخلی' => '۱۲۸ گیگابایت',
                    'صفحه نمایش' => '۶.۱ اینچ Super Retina XDR',
                    'تراشه' => 'Apple A15 Bionic',
                    'دوربین' => '۱۲ مگاپیکسل دوگانه',
                ],
                'description' => $this->describe('موبایل', 'گوشی موبایل اپل مدل iPhone 13 CH دو سیم‌کارت'),
                'highlights' => $this->highlights('موبایل'),
            ],
            [
                'slug' => 'ipad-mini-2021',
                'title' => 'تبلت اپل مدل iPad mini ۸.۳ اینچ (۲۰۲۱)',
                'brand' => 'اپل',
                'category' => 'تبلت',
                'image' => self::CDN.'1a9e53cd390a8698ec880cb29ac054645f38dcc7_163654087221621_nobg.png',
                'price' => 16990000,
                'hasVariants' => true,
                'swatches' => ['#e4e4e4', '#8a8f99', '#f2c9c9'],
                'colorNames' => ['نقره‌ای', 'خاکستری فضایی', 'صورتی'],
                'sku' => 'SLK-TB-3303',
                'stock' => 10,
                'specs' => [
                    'مدل' => 'iPad mini 2021 MK7M3LL/A',
                    'حافظه داخلی' => '۶۴ گیگابایت',
                    'صفحه نمایش' => '۸.۳ اینچ Liquid Retina',
                    'پردازنده' => 'Apple A15 Bionic',
                ],
                'description' => $this->describe('تبلت', 'تبلت اپل مدل iPad mini ۸.۳ اینچ (۲۰۲۱)'),
                'highlights' => $this->highlights('تبلت'),
            ],
            [
                'slug' => 'ipad-pro-11-2021',
                'title' => 'تبلت اپل مدل iPad Pro ۱۱ اینچ (۲۰۲۱)',
                'brand' => 'اپل',
                'category' => 'تبلت',
                'image' => self::CDN.'591ea75e8a03358aae3c05c498c83fbf66380c9e_162419192388660_nobg.png',
                'price' => 30490000,
                'hasVariants' => true,
                'swatches' => ['#e4e4e4', '#1b1b1b'],
                'colorNames' => ['نقره‌ای', 'خاکستری فضایی'],
                'sku' => 'SLK-TB-3304',
                'stock' => 7,
                'specs' => [
                    'مدل' => 'iPad Pro 11 inch 2021',
                    'حافظه داخلی' => '۱۲۸ گیگابایت',
                    'صفحه نمایش' => '۱۱ اینچ Liquid Retina XDR',
                    'پردازنده' => 'Apple M1',
                ],
                'description' => $this->describe('تبلت', 'تبلت اپل مدل iPad Pro ۱۱ اینچ (۲۰۲۱)'),
                'highlights' => $this->highlights('تبلت'),
            ],
        ];
    }

    private function describe(string $category, string $title): string
    {
        return match ($category) {
            'موبایل' => "{$title} با طراحی مدرن، عملکرد روان و دوربینی قدرتمند، تجربه‌ای متفاوت از استفاده روزمره را برایتان رقم می‌زند. این محصول به صورت اورجینال و با گارانتی معتبر از فروشگاه سالیکا عرضه می‌شود و امکان تحویل اکسپرس در تهران و ارسال به سراسر کشور را دارد.",
            'ساعت' => "{$title} همراهی هوشمند برای سلامتی و سبک زندگی فعال شماست؛ از ردیابی دقیق ورزش‌ها گرفته تا اعلان‌های آنی و باتری بادوام. طراحی شیک و بدنه مقاوم آن را برای استفاده روزانه و ورزشی ایده‌آل کرده است.",
            'تبلت' => "{$title} ترکیبی از قدرت پردازشی بالا، صفحه‌نمایش خیره‌کننده و باتری بادوام است که آن را برای کار، تحصیل و سرگرمی به انتخابی مناسب تبدیل می‌کند. سبک، جمع‌وجور و آماده همراهی در هر لحظه از روز.",
            'هدفون' => "{$title} صدایی واضح و باکیفیت را همراه با راحتی استفاده طولانی‌مدت ارائه می‌دهد. مناسب برای بازی، تماس و گوش دادن به موسیقی با اتصال پایدار و میکروفون حذف نویز.",
            default => "{$title} یکی از محصولات پرطرفدار فروشگاه سالیکا با کیفیت ساخت عالی و قیمت مناسب است.",
        };
    }

    private function highlights(string $category): array
    {
        return match ($category) {
            'موبایل' => [
                'اورجینال و دارای گارانتی رسمی شرکتی',
                'دوربین حرفه‌ای برای عکاسی در هر شرایط نوری',
                'باتری پرقدرت با شارژ سریع',
                'ارسال اکسپرس و امکان پرداخت در محل',
            ],
            'ساعت' => [
                'ردیابی دقیق ضربان قلب، خواب و ورزش‌های روزانه',
                'مقاوم در برابر آب و ضربه برای استفاده روزمره',
                'باتری بادوام با یک بار شارژ',
                'سازگار با گوشی‌های اندروید و آیفون',
            ],
            'تبلت' => [
                'پردازنده قدرتمند برای اجرای روان اپلیکیشن‌ها',
                'صفحه‌نمایش با رنگ‌های واقعی و روشنایی بالا',
                'باتری مناسب برای یک روز کامل استفاده',
                'وزن سبک و طراحی جمع‌وجور برای حمل آسان',
            ],
            'هدفون' => [
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
