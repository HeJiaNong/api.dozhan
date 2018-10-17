<?php

use Illuminate\Database\Seeder;

class AlbumsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取用户id
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        //获取分类id
        $category_ids = \App\Models\Category::all()->pluck('id')->toArray();

        $albums = factory(\App\Models\Album::class)->times(5)->make()->each(function ($model,$index)use ($user_ids,$category_ids){
            $model->user_id = array_random($user_ids);
            $model->category_id = array_random($category_ids);
        });

        \App\Models\Album::insert($albums->toArray());
    }
}
