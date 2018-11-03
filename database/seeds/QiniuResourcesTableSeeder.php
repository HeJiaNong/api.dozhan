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

        $images = $this->uploadImages();
//        $videos = $this->uploadVideos();
    }

    public function uploadImages(){
        //执行本地文件上传
        $dispatcher = app(\Dingo\Api\Dispatcher::class);

        try {
            $res = $dispatcher
                ->be(\App\Models\User::find(1))
                ->attach(['image' => './public/default/avatar/1.jpg'])
                ->post('api/resource/image',['scene' => 'avatar']);
        } catch (Dingo\Api\Exception\InternalHttpException $e) {
            //上传出错，记录日志
            \Illuminate\Support\Facades\Log::error('图片数据填充上传出错:'.json_encode($e->getResponse()));
            dd('上传出错');
        }

        dd($res);
    }

    public function uploadVideos(){
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
            $videos[$i]['id'] = uniqid('test');
            $videos[$i]['etag'] = uniqid('test');
            $videos[$i]['fsize'] = 6666;
            $videos[$i]['ext'] = '.mp4';
            $videos[$i]['endUser'] = array_random($user_ids);
            $videos[$i]['mimeType'] = "video/".substr($forks[$i],strpos($forks[$i],".")+1);
            $videos[$i]['key'] = $forks[$i];
            $videos[$i]['bucket'] = $bucket;
            $videos[$i]['created_at'] = $now;
            $videos[$i]['updated_at'] = $now;
        }

        return $videos;
    }
}
