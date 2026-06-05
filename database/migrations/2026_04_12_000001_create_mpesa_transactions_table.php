<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('mpesa_transactions')) {
            Schema::create('mpesa_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('transaction_type');
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->string('phone');
                $table->string('reference')->nullable();
                $table->string('description')->nullable();
                $table->string('status')->default('pending');
                $table->string('mpesa_receipt_number')->nullable();
                $table->timestamp('transaction_date')->nullable();
                $table->json('raw_callback_data')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index('reference');
                $table->index('transaction_id');
            });
        }
    }
    public function down()
    {
        Schema::dropIfExists('mpesa_transactions');
    }
};
