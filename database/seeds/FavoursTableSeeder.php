<?php

use Illuminate\Database\Seeder;

class FavoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //生成记录条数
        $count = 3000;

        //用户id
        $user_ids = \App\Models\User::pluck('id')->toArray();

        //作品id
        $work_ids = \App\Models\Work::pluck('id')->toArray();

        //评论id
        $comment_ids = \App\Models\Comment::pluck('id')->toArray();

        //获取work模型的表名
        $tables = [
            (new \App\Models\Work())->getTable() => $work_ids,
            (new \App\Models\Comment())->getTable() => $comment_ids,
        ];

        $favours = factory(\App\Models\Favour::class)->times($count)->make()->each(function ($model,$index)use($user_ids,$work_ids,$comment_ids,$tables){
            $model->user_id = array_random($user_ids);

            $key = array_random(array_keys($tables));

            $model->favourable_type = $key;
            $model->favourable_id = array_random($tables[$key]);

        });

        //插入数据
        \App\Models\Favour::insert($favours->toArray());

        //获取作品下的点赞数量并写入值
        \App\Models\Work::all()->each(function ($model,$index){
            $model->increment('favour_count',$model->favours->count());
        });

        //获取评论下的点赞数量并写入值
        \App\Models\Comment::all()->each(function ($model,$index){
            $model->timestamps = false;
            $model->increment('favour_count',$model->favours->count());
        });
    }
}
