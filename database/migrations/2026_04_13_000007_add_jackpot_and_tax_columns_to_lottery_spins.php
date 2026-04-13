<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->boolean('mini_jackpot_hit')->default(false)->after('win_amount');
            $table->boolean('super_jackpot_hit')->default(false)->after('mini_jackpot_hit');
            $table->boolean('free_spin_triggered')->default(false)->after('super_jackpot_hit');
            $table->decimal('tax_contribution', 10, 2)->default(0)->after('free_spin_triggered');
        });
    }

    public function down()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->dropColumn(['mini_jackpot_hit', 'super_jackpot_hit', 'free_spin_triggered', 'tax_contribution']);
        });
    }
};
