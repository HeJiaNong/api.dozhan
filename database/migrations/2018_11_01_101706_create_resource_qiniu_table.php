<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceQiniuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources_qiniu', function (Blueprint $table) {
            $table->increments('id');
            $table->json('params')->nullable()->comment('自定义参数');
            $table->string('endUser')->nullable()->comment('上传时指定的endUser字段，通常用于区分不同终端用户的请求');
            $table->string('persistentId')->nullable()->comment('音视频转码持久化的进度查询ID');
            $table->string('bucket')->nullable()->comment('获得上传的目标空间名');
            $table->string('key')->nullable()->comment('获得文件保存在空间中的资源名');
            $table->string('etag')->nullable()->comment('文件上传成功后的 HTTPETag。若上传时未指定资源ID，Etag将作为资源ID使用');
            $table->integer('fsize')->nullable()->comment('资源尺寸，单位为字节');
            $table->string('mimeType')->nullable()->comment('资源类型，例如JPG图片的资源类型为image/jpg');
            $table->string('imageAve')->nullable()->comment('图片主色调，算法由Camera360友情提供');
            $table->string('ext')->nullable()->comment('上传资源的后缀名，通过自动检测的mimeType 或者原文件的后缀来获取');
            $table->json('exif')->nullable()->comment('获取上传图片的Exif信息');
            $table->json('imageInfo')->nullable()->comment('获取所上传图片的基本信息');
            $table->json('avinfo')->nullable()->comment('音视频资源的元信息');
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
        Schema::dropIfExists('resources_qiniu');
    }
}
