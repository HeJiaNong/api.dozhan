<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategorygablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorygables', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->comment('分类ID');
            $table->integer('categorygable_id')->unsigned()->comment('存放专辑或者视频的 id');
            $table->string('categorygable_type')->comment('存放所属模型的类名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorygables');
    }
}
