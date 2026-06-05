<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('investments')) {
            Schema::create('investments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('plan_id')->constrained('investment_plans')->onDelete('cascade');
                $table->foreignId('machine_id')->nullable()->constrained()->onDelete('set null'); // for compatibility
                $table->decimal('amount', 15, 2);
                $table->decimal('daily_profit', 15, 2);
                $table->decimal('total_projected_profit', 15, 2);
                $table->integer('remaining_days');
                $table->string('status')->default('active');
                $table->timestamp('start_date');
                $table->timestamp('end_date');
                $table->timestamp('last_accrued_at')->nullable();
                $table->boolean('auto_reinvest')->default(false);
                $table->string('compound_type')->default('daily_payout');
                $table->decimal('early_withdrawal_penalty', 5, 2)->default(20);
                $table->integer('max_cycles')->default(1);
                $table->integer('current_cycle')->default(1);
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('end_date');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('investments');
    }
};
