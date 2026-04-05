<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('followed_traders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('trader_id')->constrained('users')->onDelete('cascade');
            $table->decimal('copy_ratio', 5, 2)->default(100); // percentage of trade amount to copy
            $table->boolean('auto_copy')->default(true);
            $table->decimal('max_copy_amount', 15, 2)->nullable(); // max KES per copy
            $table->timestamps();
            
            $table->unique(['follower_id', 'trader_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('followed_traders');
    }
};
