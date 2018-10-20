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
