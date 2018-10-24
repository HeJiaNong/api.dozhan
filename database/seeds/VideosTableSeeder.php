<?php

use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //获取当前时间并转换为字符串格式
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $bucket = config('services.qiniu.bucket');
        $faker = app(\Faker\Generator::class);

        //用户id
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();

        //j假视频链接
        $forks = [
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
            'video/' . str_random(3) . '.mp4',
        ];

        $videos = [];

        for ($i = 0;$i <= count($forks)-1;$i++){
            $videos[$i]['user_id'] = array_random($user_ids);
            $videos[$i]['mime'] = "image/".substr($forks[$i],strpos($forks[$i],".")+1);
            $videos[$i]['key'] = $forks[$i];
            $videos[$i]['bucket'] = $bucket;
            $videos[$i]['created_at'] = $now;
            $videos[$i]['updated_at'] = $now;
        }

        \App\Models\Video::insert($videos);

    }
}

