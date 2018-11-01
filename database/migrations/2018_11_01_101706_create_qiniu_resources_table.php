<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQiniuResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qiniu_resources', function (Blueprint $table) {
            $table->uuid('uuid')->comment('生成uuid');
            $table->string('end_user')->comment('上传时指定的endUser字段，通常用于区分不同终端用户的请求');
            $table->string('persistent_id')->nullable()->comment('音视频转码持久化的进度查询ID');
            $table->string('bucket')->comment('获得上传的目标空间名');
            $table->string('key')->comment('获得文件保存在空间中的资源名');
            $table->string('etag')->comment('文件上传成功后的 HTTPETag。若上传时未指定资源ID，Etag将作为资源ID使用');
            $table->integer('fsize')->comment('资源尺寸，单位为字节');
            $table->string('mime_type')->comment('资源类型，例如JPG图片的资源类型为image/jpg');
            $table->string('image_ave')->nullable()->comment('图片主色调，算法由Camera360友情提供');
            $table->string('ext')->comment('上传资源的后缀名，通过自动检测的mimeType 或者原文件的后缀来获取');
            $table->json('exif')->nullable()->comment('获取上传图片的Exif信息');
            $table->json('image_info')->nullable()->comment('获取所上传图片的基本信息');
            $table->json('avinfo')->nullable()->comment('音视频资源的元信息');
            $table->timestamps();
            $table->primary('uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qiniu_resources');
    }
}
