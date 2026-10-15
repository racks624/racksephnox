<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('deposit_requests')) {
            Schema::table('deposit_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('deposit_requests', 'transaction_code')) {
                    $table->string('transaction_code', 32)->nullable()->index()->after('reference');
                }
                if (!Schema::hasColumn('deposit_requests', 'paybill_number')) {
                    $table->string('paybill_number', 20)->nullable()->after('transaction_code');
                }
                if (!Schema::hasColumn('deposit_requests', 'account_number')) {
                    $table->string('account_number', 32)->nullable()->after('paybill_number');
                }
                if (!Schema::hasColumn('deposit_requests', 'verification_status')) {
                    $table->string('verification_status', 24)->default('pending')->index()->after('status');
                }
                if (!Schema::hasColumn('deposit_requests', 'verified_by')) {
                    $table->unsignedBigInteger('verified_by')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('deposit_requests', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }
                if (!Schema::hasColumn('deposit_requests', 'verification_notes')) {
                    $table->text('verification_notes')->nullable()->after('verified_at');
                }
                if (!Schema::hasColumn('deposit_requests', 'match_confidence')) {
                    $table->tinyInteger('match_confidence')->default(0)->after('verification_notes');
                }
                if (!Schema::hasColumn('deposit_requests', 'user_submitted_at')) {
                    $table->timestamp('user_submitted_at')->nullable()->after('match_confidence');
                }
            });
        }

        if (!Schema::hasTable('mpesa_statements')) {
            Schema::create('mpesa_statements', function (Blueprint $table) {
                $table->id();
                $table->string('file_name');
                $table->string('file_path');
                $table->integer('total_rows')->default(0);
                $table->integer('matched_rows')->default(0);
                $table->integer('unmatched_rows')->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->date('statement_from')->nullable();
                $table->date('statement_to')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('status', 24)->default('processing');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('mpesa_statement_entries')) {
            Schema::create('mpesa_statement_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('statement_id')->index();
                $table->string('receipt_number', 32)->index();
                $table->string('transaction_code', 32)->nullable()->index();
                $table->timestamp('transaction_time')->nullable();
                $table->decimal('amount', 15, 2);
                $table->string('payer_name')->nullable();
                $table->string('payer_phone', 20)->nullable();
                $table->string('account_number', 32)->nullable();
                $table->string('paybill_number', 20)->nullable();
                $table->string('match_status', 24)->default('unmatched');
                $table->unsignedBigInteger('matched_deposit_id')->nullable();
                $table->timestamp('matched_at')->nullable();
                $table->text('raw_row')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_statement_entries');
        Schema::dropIfExists('mpesa_statements');

        if (Schema::hasTable('deposit_requests')) {
            Schema::table('deposit_requests', function (Blueprint $table) {
                foreach ([
                    'transaction_code', 'paybill_number', 'account_number',
                    'verification_status', 'verified_by', 'verified_at',
                    'verification_notes', 'match_confidence', 'user_submitted_at',
                ] as $col) {
                    if (Schema::hasColumn('deposit_requests', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
