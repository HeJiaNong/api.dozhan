<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('avs',function (Blueprint $table){
            //当 album_id 对应的 albums 表数据被删除时，删除词条
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('avs',function (Blueprint $table){
            //当 album_id 对应的 albums 表数据被删除时，删除词条
            $table->dropForeign(['album_id']);
        });
    }
}
