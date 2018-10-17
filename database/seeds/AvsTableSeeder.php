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

        //频资源id
        $video_ids = \App\Models\Video::all()->pluck('id')->toArray();

        //图片id
        $image_ids = \App\Models\Image::all()->pluck('id')->toArray();

        $category_ids = \App\Models\Category::all()->pluck('id')->toArray();


        $avs = factory(\App\Models\Av::class)->times(50)->make()->each(function ($model,$index)use($user_ids,$album_ids,$video_ids,$image_ids,$category_ids){
            $model->user_id = array_random($user_ids);
            $model->album_id = array_random($album_ids);
            $model->url_id = array_random($video_ids);
            $model->cover_id = array_random($image_ids);
            $model->category_id = array_random($category_ids);

        });

        \App\Models\Av::insert($avs->toArray());
    }
}
