<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['mobile' => '09010105397'],
            [
                'name'               => 'امیر',
                'email'              => 'amirxv@dalisaa.ir',
                'mobile_verified_at' => now(),
                'email_verified_at'  => now(),
                'password'           => 'password',
                'is_admin'           => true,
            ]
        );
    }
}
