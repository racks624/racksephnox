<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ForceKyc extends Command
{
    protected $signature = 'kyc:force {user : Email or ID of the user}';
    protected $description = 'Force verify a user (bypass document upload)';

    public function handle()
    {
        $identifier = $this->argument('user');
        $user = User::where('email', $identifier)->orWhere('id', $identifier)->first();

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        $user->is_verified = true;
        $user->kyc_level = 'tier1';
        $user->save();

        $this->info("User {$user->email} is now KYC verified.");
        return 0;
    }
}
