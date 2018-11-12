<?php

use Illuminate\Database\Seeder;

class ResourcesTableSeeder extends Seeder
{
    private $dispatcher;

    public function __construct()
    {
        $this->dispatcher = app('Dingo\Api\Dispatcher');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取所有要填充的图片
        $images = $this->scanFile(public_path('seeder/resources/images'));unset($GLOBALS['result']);
        //获取所有要填充的视频
        $videos = $this->scanFile(public_path('seeder/resources/videos'));unset($GLOBALS['result']);

        dump('images uploading...');
        $this->putFile($images);
        dump('images uploaded');

        dump('videos uploading...');
        $this->putFile($videos);
        dump('videos uploaded');
    }

    //递归查看目录下的所有包含子目录的文件
    public function scanFile($path) {
        global $result;
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($path . '/' . $file)) {
                    //这里调用自身方法
                    $this->scanFile($path . '/' . $file);
                } else {
                    $result[] = $path . '/' . $file;
                }
            }
        }
        return $result;
    }

    public function putFile($files){
        try{
            foreach ($files as $file){
                $this->dispatcher
                    ->attach(['file' => $file])
                    ->post('api/resources/seeder');
            }
        }catch (Dingo\Api\Exception\InternalHttpException $e){
            dump('error');
        }
    }

}
