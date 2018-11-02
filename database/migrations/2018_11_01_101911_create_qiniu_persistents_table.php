<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQiniuPersistentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qiniu_persistents', function (Blueprint $table) {
            $table->string('id')->comment('持久化处理的进程ID，即persistent_id');
            $table->integer('code')->comment('状态码0成功，1等待处理，2正在处理，3处理失败，4通知提交失败');
            $table->string('desc')->comment('与状态码相对应的详细描述');
            $table->string('inputBucket')->comment('处理源文件所在的空间名');
            $table->string('inputKey')->comment('处理源文件的文件名');
            $table->string('pipeline')->comment('云处理操作的处理队列');
            $table->string('reqid')->comment('云处理请求的请求id，主要用于七牛技术人员的问题排查');
            $table->json('items')->comment('云处理操作列表，包含每个云处理操作的状态信息');
            $table->timestamps();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qiniu_persistents');
    }
}
