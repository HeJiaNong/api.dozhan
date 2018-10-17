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

        $comments = factory(\App\Models\Comment::class)->times(300)->make()->each(function ($model,$index)use($user_ids,$av_ids){
            $model->user_id = array_random($user_ids);
            $model->av_id = array_random($av_ids);
        });

        \App\Models\Comment::insert($comments->toArray());
    }
}
