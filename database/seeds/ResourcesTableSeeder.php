<?php

use Illuminate\Database\Seeder;

class ResourcesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(\App\Handlers\QiniuCloudHandler $handler)
    {
        $prefix = 'seeder/';
        $this->seedFiles($handler->listFiles($handler->bucket,$prefix)['items']);
    }

    //通过获取的七牛文件信息，填充数据
    public function seedFiles($files){
        $insert = [];

        foreach ($files as $k => $file){
            $insert[$k]['endUser'] = 1;
            $insert[$k]['mimeType'] = $file['mimeType'];
            $insert[$k]['bucket'] = 'dozhan';
            $insert[$k]['key'] = $file['key'];
            $insert[$k]['etag'] = $file['hash'];
            $insert[$k]['fsize'] = $file['fsize'];
        }

        \App\Models\ResourceQiniu::insert($insert);



        \App\Models\ResourceQiniu::all()->each(function ($model,$index){
            $resource = new \App\Models\Resource();
            $resource->id = \Webpatser\Uuid\Uuid::generate(4);
            $resource->mime = $model->mimeType;
            $resource->user_id = 1;
            $model->resource()->save($resource);
        });
    }
}
