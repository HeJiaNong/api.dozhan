<?php

use Illuminate\Database\Seeder;

class WorksTableSeeder extends Seeder
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
        $user_ids = \App\Models\User::pluck('id')->toArray();

        //分类id
        $category_ids = \App\Models\Category::pluck('id')->toArray();

        //标签id
        $tag_ids = \App\Models\Tag::pluck('id')->toArray();


        //视频url
        $video_urls = \App\Models\QiniuResource::where('mimeType','video/mp4')->pluck('uuid')->toArray();

        //图片url
        $image_urls = \App\Models\QiniuResource::where('mimeType','image/webp')->pluck('uuid')->toArray();

        $works = factory(\App\Models\Work::class)->times(50)->make()->each(function ($model,$index)use($user_ids,$video_urls,$image_urls,$category_ids){
            $model->user_id = array_random($user_ids);
            $model->category_id = array_random($category_ids);
            $model->resource_url = array_random($video_urls);
            $model->cover_url = array_random($image_urls);
        });

        \App\Models\Work::insert($works->toArray());

        \App\Models\Work::all()->each(function ($model,$index)use($tag_ids){
            $model->tags()->attach(array_random($tag_ids,mt_rand(1,count($tag_ids))));
        });

        \App\Models\Tag::all()->each(function ($model,$index){
            $model->use_count = $model->works()->count();
            $model->save();
        });
    }
}
