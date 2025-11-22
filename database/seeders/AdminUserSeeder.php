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
            ['email' => 'okto@gmail.com'],
            [
                'name' => 'Owner Resto',
                'phone' => '081234567890',
                'birth_date' => '1990-01-01',
                'profile_photo' => null,
                'password' => Hash::make('popo'),
                'role' => 'admin',
                'status' => 'approved', // admin langsung approved
            ]
        );
    }
}
