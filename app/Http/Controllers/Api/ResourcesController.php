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
    //上传视频
    public function video(VideoRequest $request,QiniuCloudHandler $qiniu){

        list($key,$token) = $this->videoToken($qiniu);

        $res = $qiniu->uploadFile($request->file('video')->getRealPath(),$key,$token);

        dd($res);
    }

    //上传图片
    public function image(ImageRequest $request,QiniuCloudHandler $qiniu){

        $scene = $request->scene;

        list($key,$token) = $this->imageToken($scene,$qiniu);

        $res = $qiniu->uploadFile($request->file('image')->getRealPath(),$key,$token);

        dd($res);
    }

    //生成视频上传凭证
    public function videoToken(QiniuCloudHandler $qiniu){
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
    public function imageToken($scene,QiniuCloudHandler $qiniu){
        $prefix = 'video/';
        $mimeType = 'webp';

        $key = $qiniu->makeFileNameByTime($prefix,$mimeType);
        $entry = s($qiniu->bucket . ":" . $key);

        $size = $this->getStandardImageSize($scene);
        //命令
        $persistentOps = "imageMogr2/auto-orient/thumbnail/{$size}!/format/{$mimeType}"."|saveas/{$entry}";

        //上传策略
        $policy = $qiniu->makeUploadPolicy($key,$persistentOps);

        //上传token
        return $qiniu->makeUploadToken($key,$policy);
    }

    //上传视频回调地址
    public function videoNotification(Request $request){
        logger('七牛视频处理回调地址>>>>>>>>>>>>>>>>>>>>');
        logger($request->all());
        logger('七牛视频处理回调地址<<<<<<<<<<<<<<<<<<<<');
    }

    //上传图片回调地址
    public function imageNotification(Request $request){
        logger('七牛图片处理回调地址>>>>>>>>>>>>>>>>>>>>');
        logger($request->all());
        logger('七牛图片处理回调地址<<<<<<<<<<<<<<<<<<<<');
    }

    //获取不同理想对应的不同的标准尺寸
    public function getStandardImageSize($scene){
        switch ($scene){
            case 'avatar':
                return '200x200';
                break;
            case 'banner':
                return '1024x400';
                break;
            case 'cover':
                return '480x300';
                break;
            default:
                abort(422);
                break;
        }
        }


}
