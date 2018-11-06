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
        $faker = app(Faker\Generator::class);

        //用户id
        $user_ids = \App\Models\User::pluck('id')->toArray();

        //分类id
        $category_ids = \App\Models\Category::pluck('id')->toArray();

        //标签id
        $tag_ids = \App\Models\Tag::pluck('id')->toArray();

        //视频资源id
        $video_ids = \App\Models\ResourceQiniu::where('mimeType','video/mp4')->pluck('id')->toArray();

        //图片资源id
        $cover_ids = \App\Models\ResourceQiniu::where('key', 'like', '%cover%')->limit(count($video_ids))->pluck('id')->toArray();

        $works = factory(\App\Models\Work::class)->times(count($video_ids))->make()->each(function ($model,$index)use($faker,$user_ids,$video_ids,$cover_ids,$category_ids){
            $model->user_id = array_random($user_ids);
            $model->category_id = array_random($category_ids);
            $model->video_id = $faker->unique()->randomElement($video_ids);
            $model->cover_id = $faker->unique()->randomElement($cover_ids);
        });

        //插入数据
        \App\Models\Work::insert($works->toArray());

        //生成标签数据
        \App\Models\Work::all()->each(function ($model,$index)use($tag_ids){
            $model->tags()->attach(array_random($tag_ids,mt_rand(1,count($tag_ids))));
        });

        //为标签数据添加use_count字段统计
        \App\Models\Tag::all()->each(function ($model,$index){
            $model->use_count = $model->works()->count();
            $model->save();
        });
    }
}
