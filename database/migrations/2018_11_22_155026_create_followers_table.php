<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {

            $table->integer('follower_id')->unsigned()->comment('粉丝id');

            $table->integer('user_id')->unsigned()->comment('被订阅用户id');

            $table->timestamps();
        });

        Schema::table('users',function (Blueprint $table){
            $table->integer('followers_count')->after('qq')->default(0)->comment('粉丝数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
        Schema::table('users',function (Blueprint $table){
            $table->dropColumn('followers_count');
        });
    }
}
