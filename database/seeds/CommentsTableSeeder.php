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

        //todo 评论规范数据填充

        //填充评论表数据
        $comments = factory(\App\Models\Comment::class)->times($count)->make()->each(function ($model,$index)use($user_ids,$av_ids,$count){
            $model->user_id = array_random($user_ids);
            $model->av_id = array_random($av_ids);
            //todo 如果父类id也有父类id，那么这2个id要相同
            $model->parent_id = array_random([rand(1,$count),null]);
            $model->target_id = array_random([array_random($user_ids),null]);
        });

        //插入数据
        \App\Models\Comment::insert($comments->toArray());

        //获取视频下的评论数量并写入值
        \App\Models\Av::all()->each(function ($model,$index){
            $model->increment('comment_count',$model->comment->count());
        });
    }
}
