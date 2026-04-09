<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\TradingAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin already exists
        $admin = User::where('email', 'admin@racksephnox.com')->first();
        
        if (!$admin) {
            // Create admin user
            $admin = User::create([
                'name' => 'Super Administrator',
                'email' => 'admin@racksephnox.com',
                'phone' => '+254700000000',
                'referral_code' => strtoupper(Str::random(8)),
                'password' => Hash::make('admin123'),
                'is_admin' => true,
                'is_verified' => true,
                'kyc_status' => 'verified',
                'kyc_level' => 'tier3',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Admin user created: admin@racksephnox.com / admin123');
        } else {
            $this->command->info('ℹ️ Admin user already exists');
        }
        
        // Create wallet for admin (if not exists)
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'currency' => 'KES',
                'balance' => 100000.00,
                'locked_balance' => 0,
            ]
        );
        
        $this->command->info('✅ Admin wallet balance: KES ' . number_format($wallet->balance, 2));
        
        // Create welcome bonus transaction (if not exists)
        $bonusExists = Transaction::where('user_id', $admin->id)
            ->where('type', 'bonus')
            ->exists();
            
        if (!$bonusExists) {
            // Check what columns exist before inserting
            $columns = \DB::getSchemaBuilder()->getColumnListing('transactions');
            
            $transactionData = [
                'user_id' => $admin->id,
                'type' => 'bonus',
                'amount' => 888.00,
                'description' => 'Divine Welcome Bonus (8888 Hz Wealth Frequency)',
                'balance_after' => $wallet->balance,
            ];
            
            // Add optional columns only if they exist
            if (in_array('wallet_id', $columns)) {
                $transactionData['wallet_id'] = $wallet->id;
            }
            if (in_array('status', $columns)) {
                $transactionData['status'] = 'completed';
            }
            if (in_array('reference', $columns)) {
                $transactionData['reference'] = 'WELCOME_BONUS_' . $admin->id;
            }
            
            Transaction::create($transactionData);
            $this->command->info('✅ Welcome bonus credited: KES 888.00');
        }
        
        // Create trading account for admin (if not exists)
        $tradingAccount = TradingAccount::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'balance' => 50000.00,
            ]
        );
        
        // Add btc_balance separately if column exists
        $columns = \DB::getSchemaBuilder()->getColumnListing('trading_accounts');
        if (in_array('btc_balance', $columns) && $tradingAccount->btc_balance === null) {
            $tradingAccount->btc_balance = 0;
            $tradingAccount->save();
        }
        
        $this->command->info('✅ Trading account created with KES 50,000');
        
        // Display summary
        $this->command->newLine();
        $this->command->line('═══════════════════════════════════════════════════════════════════');
        $this->command->line('✨ ADMIN USER READY FOR DIVINE OPERATIONS ✨');
        $this->command->line('═══════════════════════════════════════════════════════════════════');
        $this->command->line('   Email:    admin@racksephnox.com');
        $this->command->line('   Password: admin123');
        $this->command->line('   Wallet:   KES ' . number_format($wallet->balance, 2));
        $this->command->line('   Trading:  KES ' . number_format($tradingAccount->balance, 2));
        $this->command->line('═══════════════════════════════════════════════════════════════════');
    }
}
