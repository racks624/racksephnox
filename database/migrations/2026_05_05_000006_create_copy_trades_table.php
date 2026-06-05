<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('copy_trades')) {
            Schema::create('copy_trades', function (Blueprint $table) {
                $table->id();
                $table->foreignId('original_order_id')->constrained('trade_orders')->onDelete('cascade');
                $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
                $table->decimal('original_amount', 15, 8);
                $table->decimal('copied_amount', 15, 8);
                $table->decimal('original_price', 15, 2);
                $table->decimal('copied_kes', 15, 2);
                $table->enum('side', ['buy', 'sell']);
                $table->enum('status', ['executed', 'failed'])->default('executed');
                $table->timestamps();
            });
        }
    }
    public function down() { Schema::dropIfExists('copy_trades'); }
};
