<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trade_orders', function (Blueprint $table) {
            $table->enum('side', ['buy', 'sell'])->change();
            $table->enum('order_type', ['market', 'limit', 'stop_loss', 'take_profit'])->default('market')->after('side');
            $table->decimal('stop_price', 15, 2)->nullable()->after('limit_price');
            $table->decimal('filled_amount', 15, 8)->default(0)->after('amount_btc');
            $table->decimal('filled_kes', 15, 2)->default(0)->after('filled_amount');
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled', 'triggered'])->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('trade_orders', function (Blueprint $table) {
            $table->dropColumn(['stop_price', 'filled_amount', 'filled_kes']);
        });
    }
};
