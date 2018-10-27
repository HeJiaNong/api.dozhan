<?php

use Illuminate\Database\Seeder;

class ImagesTableSeeder extends Seeder
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
            $images[$i]['user_id'] = array_random($user_ids);
            $images[$i]['mime'] = "image/".substr($keys[$i],strpos($keys[$i],".")+1);
            $images[$i]['key'] = $keys[$i];
            $images[$i]['bucket'] = $bucket;
            $images[$i]['created_at'] = $now;
            $images[$i]['updated_at'] = $now;
        }

        \App\Models\Image::insert($images);
    }

}


















