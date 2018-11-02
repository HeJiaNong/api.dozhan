<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->integer('category_id')->unsigned()->comment('分类id');
            $table->string('name')->comment('作品名称');
            $table->string('description')->comment('作品描述');
            $table->string('resource_url')->comment('作品视频资源链接');
            $table->string('cover_url')->comment('作品封面图片链接');
            $table->integer('page_view')->unsigned()->default(0)->comment('作品浏览次数/PV');
            $table->integer('comment_count')->unsigned()->default(0)->comment('作品评论数量');
            $table->integer('favour_count')->unsigned()->default(0)->comment('作品点赞数量');
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
        Schema::dropIfExists('works');
    }
}
