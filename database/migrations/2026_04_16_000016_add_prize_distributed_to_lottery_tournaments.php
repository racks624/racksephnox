<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lottery_tournaments', function (Blueprint $table) {
            if (!Schema::hasColumn('lottery_tournaments', 'prize_distributed')) {
                $table->boolean('prize_distributed')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('lottery_tournaments', function (Blueprint $table) {
            $table->dropColumn('prize_distributed');
        });
    }
};
