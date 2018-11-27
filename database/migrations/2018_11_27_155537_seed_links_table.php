<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = \Carbon\Carbon::now();
        \DB::table('links')->insert([
            [
                'name' => '百度',
                'description' => '小小百度',
                'link' => 'http://www.baidu.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => '新浪',
                'description' => '小小新浪',
                'link' => 'http://www.sina.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
