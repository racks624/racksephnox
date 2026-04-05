<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@racksephnox.com',
            'phone' => '254700000000',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
            'kyc_level' => 'tier2',
            'is_verified' => true,
        ]);
    }
}
