<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            if (!Schema::hasColumn('machines', 'risk_profile')) {
                $table->string('risk_profile')->default('Medium');
            }
            if (!Schema::hasColumn('machines', 'icon')) {
                $table->string('icon')->default('fa-microchip');
            }
            if (!Schema::hasColumn('machines', 'color')) {
                $table->string('color')->default('from-gold-400 to-amber-400');
            }
            if (!Schema::hasColumn('machines', 'referral_bonus_rate')) {
                $table->decimal('referral_bonus_rate', 5, 2)->default(5);
            }
            if (!Schema::hasColumn('machines', 'early_withdrawal_penalty')) {
                $table->decimal('early_withdrawal_penalty', 5, 2)->default(20);
            }
        });
    }

    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn(['risk_profile', 'icon', 'color', 'referral_bonus_rate', 'early_withdrawal_penalty']);
        });
    }
};
