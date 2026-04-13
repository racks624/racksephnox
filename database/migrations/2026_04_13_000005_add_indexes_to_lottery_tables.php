<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
            $table->index('win_amount');
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('lottery_games', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('progressive_jackpot');
        });

        Schema::table('lottery_symbols', function (Blueprint $table) {
            $table->index('weight');
            $table->index('is_wild');
        });
    }

    public function down()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['win_amount']);
            $table->dropIndex(['user_id', 'created_at']);
        });
        Schema::table('lottery_games', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['progressive_jackpot']);
        });
        Schema::table('lottery_symbols', function (Blueprint $table) {
            $table->dropIndex(['weight']);
            $table->dropIndex(['is_wild']);
        });
    }
};
