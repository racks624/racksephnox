<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lottery_games', function (Blueprint $table) {
            $table->decimal('base_rtp', 5, 2)->default(95.00)->after('jackpot_contribution_rate');
            $table->decimal('vip_rtp', 5, 2)->default(97.00)->after('base_rtp');
            $table->decimal('promo_rtp', 5, 2)->default(99.00)->after('vip_rtp');
        });
    }

    public function down()
    {
        Schema::table('lottery_games', function (Blueprint $table) {
            $table->dropColumn(['base_rtp', 'vip_rtp', 'promo_rtp']);
        });
    }
};
