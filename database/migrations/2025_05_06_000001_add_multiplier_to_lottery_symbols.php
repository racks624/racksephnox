<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::table('lottery_symbols', function (Blueprint $table) {
            if (!Schema::hasColumn('lottery_symbols', 'multiplier')) {
                $table->decimal('multiplier', 4, 2)->default(1)->after('is_scatter');
            }
        });
    }
    public function down() {
        Schema::table('lottery_symbols', function (Blueprint $table) {
            $table->dropColumn('multiplier');
        });
    }
};
