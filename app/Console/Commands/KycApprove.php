<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\KycDocument;
use Illuminate\Console\Command;

class KycApprove extends Command
{
    protected $signature = 'kyc:approve {user? : The email or ID of the user} {--all : Approve all pending KYC documents}';
    protected $description = 'Approve KYC documents for a user or all pending users';

    public function handle()
    {
        if ($this->option('all')) {
            $documents = KycDocument::where('status', 'pending')->get();
            foreach ($documents as $doc) {
                $this->approveDocument($doc);
            }
            $this->info("Approved {$documents->count()} pending KYC documents.");
            return 0;
        }

        $userIdentifier = $this->argument('user');
        if (!$userIdentifier) {
            $this->error('Please provide a user email/ID or use --all flag.');
            return 1;
        }

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

        $this->approveDocument($document);
        $this->info("KYC approved for user: {$user->email}");
        return 0;
    }

    private function approveDocument($document)
    {
        $document->update([
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $document->user->update([
            'is_verified' => true,
            'kyc_level' => 'tier1', // or appropriate level
        ]);
    }
}
