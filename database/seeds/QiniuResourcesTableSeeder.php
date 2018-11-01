<?php

use Illuminate\Database\Seeder;

class QiniuResourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images = $this->images();
        $videos = $this->videos();

        \App\Models\QiniuResource::insert($images);
        \App\Models\QiniuResource::insert($videos);
//        dd(\App\Models\QiniuResource::all());
    }

    public function images(){
        //获取当前时间并转换为字符串格式
        $now = \Carbon\Carbon::now()->toDateTimeString();
        //获取七牛空间名
        $bucket = config('services.qiniu.bucket');

        //预设图片地址
        $keys = [
            'image/avatar/1.webp',
            'image/avatar/2.webp',
            'image/avatar/3.webp',
            'image/avatar/4.webp',
            'image/avatar/5.webp',
            'image/avatar/6.webp',
            'image/avatar/7.webp',
            'image/avatar/8.webp',
            'image/avatar/9.webp',
            'image/banner/1.webp',
            'image/banner/2.webp',
            'image/banner/3.webp',
            'image/banner/4.webp',
            'image/banner/5.webp',
            'image/cover/1.webp',
            'image/cover/2.webp',
            'image/cover/3.webp',
            'image/cover/4.webp',
            'image/cover/5.webp',
        ];

        //用户id
        $user_ids = \App\Models\User::pluck('id')->toArray();

        $images = [];

        for ($i = 0;$i <= count($keys)-1;$i++){
            $images[$i]['uuid'] = str_random('10');
            $images[$i]['etag'] = str_random('10');
            $images[$i]['fsize'] = 6666;
            $images[$i]['ext'] = '.webp';
            $images[$i]['end_user'] = array_random($user_ids);
            $images[$i]['mime_type'] = "image/".substr($keys[$i],strpos($keys[$i],".")+1);
            $images[$i]['key'] = $keys[$i];
            $images[$i]['bucket'] = $bucket;
            $images[$i]['created_at'] = $now;
            $images[$i]['updated_at'] = $now;
        }

        return $images;
    }

    public function videos(){
        //获取当前时间并转换为字符串格式
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $bucket = config('services.qiniu.bucket');
        $faker = app(\Faker\Generator::class);

        //用户id
        $user_ids = \App\Models\User::pluck('id')->toArray();

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
            $videos[$i]['uuid'] = str_random('10');
            $videos[$i]['etag'] = str_random('10');
            $videos[$i]['fsize'] = 6666;
            $videos[$i]['ext'] = '.mp4';
            $videos[$i]['end_user'] = array_random($user_ids);
            $videos[$i]['mime_type'] = "video/".substr($forks[$i],strpos($forks[$i],".")+1);
            $videos[$i]['key'] = $forks[$i];
            $videos[$i]['bucket'] = $bucket;
            $videos[$i]['created_at'] = $now;
            $videos[$i]['updated_at'] = $now;
        }

        return $videos;
    }
}
