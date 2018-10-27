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
        $user_ids = \App\Models\User::pluck('id')->toArray();

        //作品id
        $work_ids = \App\Models\Work::pluck('id')->toArray();

        //生成条数
        $count = 500;

        //获取work模型的表名
        $table = (new \App\Models\Work())->getTable();



        //填充评论表数据
        $comments = factory(\App\Models\Comment::class)->times($count)->make()->each(function ($model,$index)use($user_ids,$work_ids,$count,$table){
            $model->user_id = array_random($user_ids);
            $model->commentable_id = array_random($work_ids);
            $model->commentable_type = $table;
        });

        //插入数据
        \App\Models\Comment::insert($comments->toArray());

        //获取作品下的点赞数量并写入值
        \App\Models\Work::all()->each(function ($model,$index){
            $model->increment('comment_count',$model->comments->count());
        });

    }
}
