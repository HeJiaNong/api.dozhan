<?php

use Illuminate\Database\Seeder;

class AvTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取视频id
        $av_ids = \App\Models\Av::all()->pluck('id')->toArray();

        //获取标签id
        $tag_ids = \App\Models\Tag::all()->pluck('id')->toArray();

        $data = [];

        for ($i = 0;$i <= count($av_ids)-1;$i++){
            $data[$i]['av_id'] = array_random($av_ids);
            $data[$i]['tag_id'] = array_random($tag_ids);
        }

        \Illuminate\Support\Facades\DB::table('av_tag')->insert($data);
    }
}
