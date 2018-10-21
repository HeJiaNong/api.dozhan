<?php

use Illuminate\Database\Seeder;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //用户id
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        //视频id
        $av_ids = \App\Models\Av::all()->pluck('id')->toArray();

        $count = 500;

        //填充评论表数据
        $comments = factory(\App\Models\Comment::class)->times($count)->make()->each(function ($model,$index)use($user_ids,$av_ids,$count){
            $model->user_id = array_random($user_ids);
            $model->av_id = array_random($av_ids);
            //如果父类id也有父类id，那么这2个id要相同
            $model->parent_id = array_random([rand(1,$count),null]);
            $model->target_id = array_random([array_random($user_ids),null]);
        });

        \App\Models\Comment::insert($comments->toArray());
    }
}
