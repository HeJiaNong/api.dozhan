<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('标签名称');
            $table->integer('use_count')->unsigned()->default(0)->comment('使用次数');
            $table->timestamps();
        });

        /*
         * 与作品多对多关系，建立 tag_work 中间关系表
         */
        Schema::create('tag_work', function (Blueprint $table) {
            $table->integer('tag_id')->unsigned()->comment('标签id');
            $table->integer('work_id')->unsigned()->comment('作品id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tag_work');
    }
}
