<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->boolean('auto_reinvest')->default(false)->after('status');
            $table->enum('compound_type', ['daily_payout', 'reinvest'])->default('daily_payout')->after('auto_reinvest');
            $table->decimal('early_withdrawal_penalty', 5, 2)->default(5.00)->after('compound_type'); // percentage
            $table->timestamp('early_withdrawn_at')->nullable()->after('early_withdrawal_penalty');
            $table->decimal('early_withdrawn_amount', 15, 2)->nullable()->after('early_withdrawn_at');
            $table->integer('current_cycle')->default(1)->after('early_withdrawn_amount');
            $table->integer('max_cycles')->default(1)->after('current_cycle');
        });
    }

    public function down()
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['auto_reinvest', 'compound_type', 'early_withdrawal_penalty', 'early_withdrawn_at', 'early_withdrawn_amount', 'current_cycle', 'max_cycles']);
        });
    }
};
