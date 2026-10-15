<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('deposit_requests')) return;

        Schema::table('deposit_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('deposit_requests', 'transaction_code')) {
                $table->string('transaction_code')->nullable()->index()->after('reference');
            }
            if (!Schema::hasColumn('deposit_requests', 'payment_date')) {
                $table->date('payment_date')->nullable()->after('transaction_code');
            }
            if (!Schema::hasColumn('deposit_requests', 'phone')) {
                $table->string('phone')->nullable()->after('payment_date');
            }
            if (!Schema::hasColumn('deposit_requests', 'paybill')) {
                $table->string('paybill')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('deposit_requests', 'account_number')) {
                $table->string('account_number')->nullable()->after('paybill');
            }
            if (!Schema::hasColumn('deposit_requests', 'verification_method')) {
                $table->string('verification_method')->nullable()->after('status');
            }
            if (!Schema::hasColumn('deposit_requests', 'verification_notes')) {
                $table->text('verification_notes')->nullable()->after('verification_method');
            }
            if (!Schema::hasColumn('deposit_requests', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_notes');
            }
            if (!Schema::hasColumn('deposit_requests', 'verified_by')) {
                $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            }
            if (!Schema::hasColumn('deposit_requests', 'mpesa_receipt')) {
                $table->text('mpesa_receipt')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('deposit_requests', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
            if (!Schema::hasColumn('deposit_requests', 'user_agent')) {
                $table->text('user_agent')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deposit_requests', function (Blueprint $table) {
            $cols = [
                'transaction_code','payment_date','phone','paybill','account_number',
                'verification_method','verification_notes','verified_at','verified_by',
                'mpesa_receipt','ip_address','user_agent',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('deposit_requests', $c)) $table->dropColumn($c);
            }
        });
    }
};
