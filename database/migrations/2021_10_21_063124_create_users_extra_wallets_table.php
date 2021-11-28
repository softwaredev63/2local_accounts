<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersExtraWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_extra_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            
            $table->string('xlm_private_key')->nullable()->length(512);
            $table->string('xlm_public_key')->nullable()->length(512);
            $table->string('btc_private_key')->nullable();
            $table->string('btc_public_key')->nullable();
            $table->string('eth_private_key')->nullable();
            $table->string('eth_public_key')->nullable();
            $table->double('balance_l2l')->default(0.0);
            $table->double('balance_xlm')->default(0.0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_extra_wallets');
    }
}
