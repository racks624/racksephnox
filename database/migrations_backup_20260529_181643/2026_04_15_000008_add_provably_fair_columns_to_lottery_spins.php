<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->string('client_seed')->nullable()->after('tax_contribution');
            $table->string('server_seed')->nullable()->after('client_seed');
            $table->string('server_seed_hashed')->nullable()->after('server_seed');
            $table->unsignedInteger('nonce')->default(0)->after('server_seed_hashed');
            $table->boolean('verified')->default(false)->after('nonce');
        });
    }

    public function down()
    {
        Schema::table('lottery_spins', function (Blueprint $table) {
            $table->dropColumn(['client_seed', 'server_seed', 'server_seed_hashed', 'nonce', 'verified']);
        });
    }
};
