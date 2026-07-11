<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    private const CITIES = [
        'تهران' => ['تهران'],
        'اصفهان' => ['اصفهان', 'کاشان'],
        'خراسان رضوی' => ['مشهد', 'نیشابور'],
        'فارس' => ['شیراز', 'مرودشت'],
        'آذربایجان شرقی' => ['تبریز'],
        'البرز' => ['کرج'],
        'خوزستان' => ['اهواز'],
        'گیلان' => ['رشت'],
    ];

    public function definition(): array
    {
        $province = fake()->randomElement(array_keys(self::CITIES));
        $city = fake()->randomElement(self::CITIES[$province]);

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['خانه', 'محل کار', 'آدرس دوم']),
            'receiver_name' => fake()->name(),
            'receiver_mobile' => '09'.fake()->numerify('#########'),
            'province' => $province,
            'city' => $city,
            'address_line' => fake()->streetAddress().'، پلاک '.fake()->numberBetween(1, 200).'، واحد '.fake()->numberBetween(1, 12),
            'postal_code' => fake()->numerify('##########'),
            'is_default' => false,
        ];
    }
}
