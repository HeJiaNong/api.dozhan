<?php

namespace App\Http\Controllers\Api;

use App\Handlers\QiniuCloudHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    //上传图片
    public function image(ImageRequest $request,QiniuCloudHandler $qiniu){
        //接收文件
        $file = $request->image;

        //生成名称
        $filename = $qiniu->overwriteKey($file,"image/{$request->type}/");

        $size = $this->getStandardSize($request);

        //持久化处理指令列表
        $persistentOps = "imageMogr2/auto-orient/thumbnail/{$size}!/format/webp"."|saveas/{$qiniu->makeSaveasUrl($filename)}";

        //上传
        $res = $qiniu->uploadFile($file,$persistentOps,$filename);

        if (!$res){
            return $this->response->error('上传失败');
        }

        $data = [
            'user_id' => Auth::guard('api')->id(),
            'type' => $request->type,
            'mime' => $res['mimeType'],
            'url' => $res['url'],
            'bucket' => $res['bucket'],
        ];

        $image = Image::create($data);

        return $this->response->item($image,new ImageTransformer())->setStatusCode(201);
    }

    public function getStandardSize($request){
        switch ($request->type){
            case 'avatar':
                $size =  '200x200';
                break;
            case 'banner':
                $size = '1024x400';
                break;
            case 'video_cover':
                $size = '480x300';
                break;
        }

        return $size;
    }
}
