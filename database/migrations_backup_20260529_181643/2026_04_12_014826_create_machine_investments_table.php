<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('machine_investments')) {
            Schema::create('machine_investments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('machine_id')->constrained()->onDelete('cascade');
                $table->integer('vip_level');
                $table->decimal('amount', 15, 2);
                $table->decimal('daily_profit', 15, 2);
                $table->decimal('total_return', 15, 2);
                $table->date('start_date');
                $table->date('end_date');
                $table->string('status')->default('active');
                $table->decimal('profit_credited', 15, 2)->default(0);
                $table->date('last_profit_date')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'status']);
                $table->index(['machine_id', 'status']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('machine_investments');
    }
};
