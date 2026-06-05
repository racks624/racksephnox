<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('trading_accounts', 'btc_balance')) {
                $table->decimal('btc_balance', 16, 8)->default(0)->after('locked_balance');
            }
        });
    }

    public function down()
    {
        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->dropColumn('btc_balance');
        });
    }
};
