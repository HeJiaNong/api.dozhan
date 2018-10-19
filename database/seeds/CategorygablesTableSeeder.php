<?php

use Illuminate\Database\Seeder;

class CategorygablesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_ids = \App\Models\Category::all()->pluck('id')->toArray();

        //赋值专辑分类
        $albums = \App\Models\Album::all()->each(function ($model,$index)use($category_ids){
            $model->category()->attach(array_random($category_ids));
        });

        $avs = \App\Models\Av::all()->each(function ($model,$index)use($category_ids){
            $model->category()->attach(array_random($category_ids));
        });


    }
}
