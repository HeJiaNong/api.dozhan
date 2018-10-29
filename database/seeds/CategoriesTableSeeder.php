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
        $categories = factory(\App\Models\Category::class)->times(3)->create();

        $category = \App\Models\Category::find(1);
        $category->name  = '镜头特效';
        $category->cover = 'http://pglgpkuzs.bkt.clouddn.com/image/icon/movie.svg';
        $category->save();

        $category = \App\Models\Category::find(2);
        $category->name  = '图像处理';
        $category->cover = 'http://pglgpkuzs.bkt.clouddn.com/image/icon/travel.svg';
        $category->save();

        $category = \App\Models\Category::find(3);
        $category->name  = '个人作品';
        $category->cover = 'http://pglgpkuzs.bkt.clouddn.com/image/icon/humor.svg';
        $category->save();
    }
}
