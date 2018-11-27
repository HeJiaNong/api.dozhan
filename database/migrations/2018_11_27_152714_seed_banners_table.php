<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = \Carbon\Carbon::now();
        \DB::table('banners')->insert([
            [
                'description' => '我是描述',
                'link_url' => '#',
                'img_url' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => '我是描述',
                'link_url' => '#',
                'img_url' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/2.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'description' => '我是描述',
                'link_url' => '#',
                'img_url' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/3.jpg',
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
