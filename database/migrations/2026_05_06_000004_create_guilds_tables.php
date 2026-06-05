<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lottery_guilds')) {
            Schema::create('lottery_guilds', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('tag', 6);
                $table->text('description')->nullable();
                $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
                $table->integer('member_count')->default(1);
                $table->integer('total_score')->default(0);
                $table->boolean('is_open')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('lottery_guild_members')) {
            Schema::create('lottery_guild_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guild_id')->constrained('lottery_guilds')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('role')->default('member');
                $table->integer('contribution')->default(0);
                $table->timestamps();
                $table->unique(['guild_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('lottery_guild_tournaments')) {
            Schema::create('lottery_guild_tournaments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guild_id')->constrained('lottery_guilds')->onDelete('cascade');
                $table->string('name');
                $table->timestamp('start_date');
                $table->timestamp('end_date');
                $table->decimal('prize_pool', 12, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('lottery_guild_tournaments');
        Schema::dropIfExists('lottery_guild_members');
        Schema::dropIfExists('lottery_guilds');
    }
};
