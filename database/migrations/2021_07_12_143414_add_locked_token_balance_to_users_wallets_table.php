<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockedTokenBalanceToUsersWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_wallets', function (Blueprint $table) {
            $table->decimal('balance_locked_2lc', 29, 18)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_wallets', function (Blueprint $table) {
            $table->dropColumn('balance_locked_2lc');
        });
    }
}
