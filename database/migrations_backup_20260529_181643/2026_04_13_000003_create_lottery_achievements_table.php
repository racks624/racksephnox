<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lottery_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('icon')->default('fa-trophy');
            $table->string('condition_type');
            $table->integer('condition_value');
            $table->integer('reward_free_spins')->default(0);
            $table->timestamps();
        });

        Schema::create('user_lottery_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('lottery_achievements')->onDelete('cascade');
            $table->timestamp('achieved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_lottery_achievements');
        Schema::dropIfExists('lottery_achievements');
    }
};
