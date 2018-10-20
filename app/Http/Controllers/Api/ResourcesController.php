<?php

namespace App\Http\Controllers\Api;

use App\Handlers\QiniuCloudHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Requests\Api\VideoRequest;
use App\Models\Image;
use App\Models\Video;
use App\Transformers\ImageTransformer;
use App\Transformers\VideoTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Qiniu\base64_urlSafeEncode as s;

class ResourcesController extends Controller
{

    //生成视频上传凭证
    public function videoToken(QiniuCloudHandler $qiniu){
//        dd(2);
        $prefix = 'video/';
        $mimeType = 'mp4';

        $key = $qiniu->makeFileNameByTime($prefix,$mimeType);
        $entry = s($qiniu->bucket . ":" . $key);

        //命令
        $persistentOps = "avthumb/{$mimeType}/wmText/".s('Dozhan')."/wmFontSize/40/wmFontColor/".s('#ffffff'). "/wmGravityText/NorthWest"."|saveas/{$entry}";//

        //上传策略
        $policy = $qiniu->makeUploadPolicy($key,$persistentOps);

        //上传token
        return $qiniu->makeUploadToken($key,$policy);
    }

    //生成图片上传凭证
    public function imageToken($size,QiniuCloudHandler $qiniu){
//        dd(2);
        $prefix = 'video/';
        $mimeType = 'webp';

        $key = $qiniu->makeFileNameByTime($prefix,$mimeType);
        $entry = s($qiniu->bucket . ":" . $key);

        //命令
        $persistentOps = "imageMogr2/auto-orient/thumbnail/{$size}!/format/{$mimeType}"."|saveas/{$entry}";

        //上传策略
        $policy = $qiniu->makeUploadPolicy($key,$persistentOps);

        //上传token
        return $qiniu->makeUploadToken($key,$policy);
    }

    //上传视频
    public function video(VideoRequest $request,QiniuCloudHandler $qiniu){

        list($key,$token) = $this->videoToken($qiniu);

        //上传
        $res = $qiniu->uploadFile($request->file('video')->getRealPath(),$key,$token);

        dd($res);
        if (!$res){
            return $this->response->errorInternal('上传失败');
        }

        dd($res);

        $data = [
            'user_id' => Auth::guard('api')->id(),
            'mime' => $res['mimeType'],
            'key' => $res['key'],
            'bucket' => $res['bucket'],
        ];

        $video = Video::create($data);

        return $this->response->item($video,new VideoTransformer())->setMeta($res)->setStatusCode(201);
    }


    //上传图片
    public function image(ImageRequest $request,QiniuCloudHandler $qiniu){

        //文件格式转换
        $ext = 'webp';

        //生成名称
        $filename = $qiniu->makeFileNameByTime("image/{$request->scene}/",$ext);

        //图片分辨率
        $size = $this->getStandardImageSize($request);

        //持久化处理指令列表
        $persistentOps = "imageMogr2/auto-orient/thumbnail/{$size}!/format/{$ext}"."|saveas/{$qiniu->makeSaveasUrl($filename)}";

        //上传
        $res = $qiniu->uploadFile($request->file('image')->getRealPath(),$filename,$persistentOps);

        if (!$res){
            return $this->response->errorInternal('上传失败');
        }

        $data = [
            'user_id' => Auth::guard('api')->id(),
            'scene' => $request->scene,
            'mime' => $res['mimeType'],
            'key' => $res['key'],
            'bucket' => $res['bucket'],
        ];

        $image = Image::create($data);

        return $this->response->item($image,new ImageTransformer())->setMeta($res)->setStatusCode(201);
    }

    //获取不同理想对应的不同的标准尺寸
    public function getStandardImageSize($request){
        switch ($request->scene){
            case 'avatar':
                $size =  '200x200';
                break;
            case 'banner':
                $size = '1024x400';
                break;
            case 'cover':
                $size = '480x300';
                break;
        }

        return $size;
    }


}
