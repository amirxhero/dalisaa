<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['mobile' => '09010105397'],
            [
                'name'     => 'امیر',
                'email'    => 'amirxv',
                'mobile'   => '09010105397',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );
    }
}
