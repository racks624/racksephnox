<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\KycDocument;
use Illuminate\Console\Command;

class KycReject extends Command
{
    protected $signature = 'kyc:reject {user : The email or ID of the user} {--reason= : Rejection reason}';
    protected $description = 'Reject a user\'s KYC document';

    public function handle()
    {
        $userIdentifier = $this->argument('user');
        $reason = $this->option('reason') ?? 'No reason provided.';

        $user = User::where('email', $userIdentifier)
                    ->orWhere('id', $userIdentifier)
                    ->first();

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        $document = KycDocument::where('user_id', $user->id)
                                ->where('status', 'pending')
                                ->latest()
                                ->first();

        if (!$document) {
            $this->warn('No pending KYC document found for this user.');
            return 0;
        }

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        // Optionally mark user as not verified (they remain, but KYC level unchanged)
        $this->info("KYC rejected for user: {$user->email}");
        return 0;
    }
}
