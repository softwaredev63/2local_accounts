<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBalanceTypeInUsersWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_wallets', function (Blueprint $table) {
            $table->decimal('balance_2lc', 29, 18)->change();
            $table->decimal('balance_bnb', 29, 18)->change();
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
            $table->float('balance_2lc')->change();
            $table->float('balance_bnb')->change();
        });
    }
}
