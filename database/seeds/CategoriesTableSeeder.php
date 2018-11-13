<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $icon_ids = [];

        \App\Models\ResourceQiniu::where('key','like','seeder/icon/%')->get()->each(function ($model,$index)use(&$icon_ids){
            $icon_ids[] = $model->resource->id;
        });

        $categories = factory(\App\Models\Category::class)->times(count($icon_ids))->make();


        foreach ($icon_ids as $k => $id){
            $categories[$k]->icon_id = $id;
            $categories[$k]->save();
        }

    }
}
