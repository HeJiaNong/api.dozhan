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
            $table->string('name')->unique()->index()->comment('名称');
            $table->string('introduction')->nullable()->comment('简介');
            $table->string('avatar')->comment('头像资源');
            $table->string('email')->unique()->comment('邮箱');
            $table->string('phone')->nullable()->comment('手机号码');
            $table->string('qq')->nullable()->comment('QQ号码');
            $table->integer('notification_count')->unsigned()->default(0)->comment('未读消息数量');
            $table->string('auth_token')->nullable()->comemnt('防止多设备同时登陆');
            $table->string('password')->comment('密码');
            $table->timestamps();
            $table->softDeletes();
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
