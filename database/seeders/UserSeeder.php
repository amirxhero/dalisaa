<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['mobile' => '09120000000'],
            [
                'name' => 'مدیر سالیکا',
                'email' => 'admin@salika.test',
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $this->addAddresses($admin);

        $demo = User::updateOrCreate(
            ['mobile' => '09121234567'],
            [
                'name' => 'کاربر آزمایشی',
                'email' => 'demo@salika.test',
                'email_verified_at' => now(),
                'mobile_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        $this->addAddresses($demo);

        User::factory(18)->create()->each(function (User $user) {
            $this->addAddresses($user);
        });
    }

    private function addAddresses(User $user): void
    {
        if ($user->addresses()->exists()) {
            return;
        }

        $count = random_int(1, 2);

        for ($i = 0; $i < $count; $i++) {
            Address::factory()->create([
                'user_id' => $user->id,
                'is_default' => $i === 0,
            ]);
        }
    }
}
