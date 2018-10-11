<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('do_id')->unique()->index()->comment('Dozhan提供的唯一ID');
            $table->string('name')->index()->comment('名称');
            $table->string('email')->unique()->comment('邮箱');
            $table->string('phone_number')->nullable()->comment('手机号码');
            $table->string('qq_number')->nullable()->comment('QQ号码');
            $table->string('password')->comment('密码');
            $table->rememberToken()->comment('记住用户Token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
