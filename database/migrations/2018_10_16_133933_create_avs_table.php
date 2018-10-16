<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->index()->comment('视频名称');
            $table->string('description')->comment('描述');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('album_id')->unsigned()->comment('专辑ID');
            $table->integer('url_id')->unsigned()->comment('视频地址ID');
            $table->integer('cover_id')->unsigned()->comment('封面地址ID');
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
        Schema::dropIfExists('avs');
    }
}
