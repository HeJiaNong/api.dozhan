<?php

use Illuminate\Database\Seeder;

class AvsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //todo 获取用户id，专辑id，视频id...

        //用户id
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        //专辑id
        $album_ids = \App\Models\Album::all()->pluck('id')->toArray();

        //todo 视频资源id

        $avs = factory(\App\Models\Av::class)->times(30)->make()->each(function ($model,$index){
            //
        });
    }
}
