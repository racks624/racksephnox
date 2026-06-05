<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('investment_plans')) {
            Schema::create('investment_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('min_amount', 15, 2);
                $table->decimal('max_amount', 15, 2);
                $table->decimal('daily_interest_rate', 8, 4);
                $table->integer('duration_days');
                $table->boolean('is_active')->default(true);
                $table->boolean('allow_auto_reinvest')->default(false);
                $table->boolean('allow_early_withdrawal')->default(true);
                $table->decimal('early_withdrawal_penalty', 5, 2)->default(20);
                $table->integer('max_reinvestment_cycles')->default(1);
                $table->boolean('is_infinite')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('investment_plans');
    }
};
