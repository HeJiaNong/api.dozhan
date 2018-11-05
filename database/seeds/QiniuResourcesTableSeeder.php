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
        //填充资源文件根目录
        $dir = './public/seeder/';
        $avatars = array_diff(scandir($dir.'avatar'),['.','..']);
        $banners = array_diff(scandir($dir.'banner'),['.','..']);
        $covers = array_diff(scandir($dir.'cover'),['.','..']);
        $logos = array_diff(scandir($dir.'logo'),['.','..']);
        array_walk($avatars, function (&$value, $key)use ($dir){
            $value = $dir.'avatar/'.$value;
        });
        array_walk($banners, function (&$value, $key)use ($dir){
            $value = $dir.'banner/'.$value;
        });
        array_walk($covers, function (&$value, $key)use ($dir){
            $value = $dir.'cover/'.$value;
        });
        array_walk($logos, function (&$value, $key)use ($dir){
            $value = $dir.'logo/'.$value;
        });

        //合并数组
        $images = array_merge($avatars,$banners,$covers,$logos);

        $videos = array_diff(scandir($dir.'videos'),['.','..']);

        array_walk($videos, function (&$value, $key)use ($dir){
            $value = $dir.'videos/'.$value;
        });

//        dd($images,$videos);

        dump('images uploading');
        foreach ($images as $image){
            //上传文件
            $this->upload($image,substr($image,9));
        }
        dump('images uploaded');

        dump('videos uploading');
        foreach ($videos as $video){
            //上传文件
            $this->upload($video,substr($video,9));
        }
        dump('videos uploaded');
    }

    //上传图片
    public function upload($filepath,$key = null){
        //执行本地文件上传
        $dispatcher = app(\Dingo\Api\Dispatcher::class);

        try {
            $res = $dispatcher
                ->be(\App\Models\User::find(1))
                ->attach(['file' => $filepath])
                ->post('api/resource/file',['key' => $key]);
        } catch (Dingo\Api\Exception\InternalHttpException $e) {
            //上传出错，记录日志
            \Illuminate\Support\Facades\Log::error('上传出错:'.json_encode($e->getResponse()));
            dump($key.' error.');
        }

        if (isset($res)){
            dump($key . ' ok.');
            return $res['id'];
        }
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
