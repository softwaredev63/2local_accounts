<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAffiliateCodeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'affiliate_by')->nullable();
            $table->string('affiliate_code')->unique()->nullable();
        });

        $sql = 'UPDATE users SET affiliate_code = MD5(email)';
        DB::connection()->update($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_by');
            $table->dropColumn('affiliate_code');
        });
    }
}
