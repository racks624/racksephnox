<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('trade_orders')) {
            Schema::create('trade_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('side', ['buy', 'sell', 'bonus'])->default('buy');
                $table->enum('order_type', ['market', 'limit', 'stop', 'bonus'])->default('market');
                $table->decimal('amount_btc', 15, 8);
                $table->decimal('filled_amount', 15, 8)->default(0);
                $table->decimal('limit_price', 15, 2)->nullable();
                $table->decimal('stop_price', 15, 2)->nullable();
                $table->decimal('price_per_btc', 15, 2)->nullable();
                $table->decimal('filled_kes', 15, 2)->default(0);
                $table->enum('status', ['pending', 'partial', 'completed', 'cancelled'])->default('pending');
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index(['side', 'order_type', 'status']);
            });
        }
    }
    public function down() { Schema::dropIfExists('trade_orders'); }
};
