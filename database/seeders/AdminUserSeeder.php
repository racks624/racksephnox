<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@racksephnox.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+254711111111',
                'referral_code' => strtoupper(Str::random(8)),
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'is_verified' => true,
                'kyc_status' => 'verified',
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->wallet) {
            $admin->wallet()->create(['balance' => 100000]);
        }

        $this->command->info('✅ Admin user ready: admin@racksephnox.com / admin123');
    }
}
