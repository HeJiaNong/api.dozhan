<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content')->comment('评论内容');
            $table->integer('favour_count')->unsigned()->default(0)->comment('点赞人数');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->integer('parent_id')->unsigned()->nullable()->comment('父评论id');
            $table->integer('target_id')->unsigned()->nullable()->comment('目标用户');
            $table->morphs('commentable');
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
        Schema::dropIfExists('comments');
    }
}
