<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic local test data.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
            AdminUserSeeder::class,
            ReviewSeeder::class,
            OrderSeeder::class,
            WishlistSeeder::class,
        ]);
    }
}
