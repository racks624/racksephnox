<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->decimal('vip1_start_amount', 10, 2);
            $table->decimal('vip2_start_amount', 10, 2);
            $table->decimal('vip3_start_amount', 10, 2);
            $table->integer('duration_days')->default(14);
            $table->decimal('growth_rate', 5, 2)->default(25.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('machines');
    }
};
