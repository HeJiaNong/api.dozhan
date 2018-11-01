<?php

namespace App\Http\Controllers\Api;

use App\Handlers\QiniuCloudHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Requests\Api\VideoRequest;
use Dingo\Api\Routing\UrlGenerator;
use Illuminate\Http\Request;

use Qiniu\Auth;
use function Qiniu\base64_urlSafeEncode;
use Qiniu\Config;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

class ResourcesController extends Controller
{

    //七牛持久化处理状态通知回调地址
    public function notification(Request $request){

        logger('================七牛持久化处理状态通知回调地址================');
        logger($request->all());
        logger('================七牛持久化处理状态通知回调地址================');
    }

    /*
     * 七牛文件上传回调地址
     */
    public function qiniuCallback(Request $request,QiniuCloudHandler $handler){
        logger('================七牛CallbackURL================');
        logger($request->all());
        logger('================七牛CallbackURL================');
        $url = "$handler->domain/$request->key";
        return $this->response->array(compact('url'))->header('Content-Type','application/json');
        /*返回结果字段如下：
          'uuid' => '9bc16085-cfa7-495c-9bd7-6e4a6fe48f82',
          'endUser' => '5',
          'persistentId' => 'z2.5bda5e94e3d00409792f6102',
          'bucket' => 'dozhan',
          'key' => '5bda5e8381acd',
          'etag' => 'FqgLGY7h2wu-gcQ8GX1o0GBouL-p',
          'fsize' => 2378354,
          'mimeType' => 'video/mp4',
          'imageAve' => NULL,
          'ext' => '.mp4',
          'exif' => NULL,
          'imageInfo' => NULL,
          'avinfo' =>
         */
    }

    //上传视频
    public function video(VideoRequest $request,QiniuCloudHandler $qiniu){
        //文件名
        $key = uniqid();

        //文件本地路径
        $filepath = $request->file('video')->getRealPath();

        //自定义变量
        $params = [];

        //水印文件另存编码
        $saveWmEntry = base64_urlSafeEncode($qiniu->bucket . ":$key" . '[mp4,wm]');
        //切片文件另存编码
        $saveHlsEntry = base64_urlSafeEncode($qiniu->bucket . ":$key" . '[hls,wm]');
        //切片ts文件名称前缀
        $hlsName = base64_urlSafeEncode("$key"."[hls,wm]"."($(count))");
        //水印文字
        $wmText = base64_urlSafeEncode('Dozhan');
        //水印颜色
        $wmColor = base64_urlSafeEncode('white');

        //转码MP4+水印
        $avthumbWmFop = "avthumb/mp4/wmText/$wmText/wmGravityText/NorthWest/wmFontColor/$wmColor/wmFontSize/50|saveas/" . $saveWmEntry;
        //转码HLS+水印
        $avthumbHlsFop = "avthumb/m3u8/noDomain/1/segtime/20/vb/5m/pattern/$hlsName/r/60/wmText/$wmText/wmGravityText/NorthWest/wmFontColor/$wmColor/wmFontSize/50|saveas/" . $saveHlsEntry;

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback'),
            'callbackBody' => '{
                "uuid"          : $(uuid),
                "endUser"       : $(endUser),
                "persistentId"  : $(persistentId),
                "bucket"        : $(bucket),
                "key"           : $(key),
                "etag"          : $(etag),
                "fsize"         : $(fsize),
                "mimeType"      : $(mimeType),
                "imageAve"      : $(imageAve),
                "ext"           : $(ext),
                "exif"          : $(exif),
                "imageInfo"     : $(imageInfo),
                "avinfo"        : $(avinfo)
            }',
            'callbackBodyType' => 'application/json',

            'persistentOps' => "$avthumbWmFop;$avthumbHlsFop",
            'persistentPipeline' => $qiniu->pipeline,
            'persistentNotifyUrl' => $qiniu->notify_url,
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        //上传文件
        $res = $qiniu->putFile($token,$key,$filepath,$params);

        return $this->response->array($res);
    }

    //上传图片
    public function image(ImageRequest $request,QiniuCloudHandler $qiniu){

        //图片缩放尺寸
        $size = $this->getStandardImageSize($request->scene);

        //文件名
        $key = uniqid();

        //文件本地路径
        $filepath = $request->file('image')->getRealPath();

        //自定义变量
        $params = [];

        //水印文件另存编码
        $saveWmEntry = base64_urlSafeEncode($qiniu->bucket . ":$key" . '[webp,thumbnail]');

        //转码webp+缩放
        $imageMogr2WebpFop = "imageMogr2/auto-orient/thumbnail/$size!/format/webp"."|saveas/$saveWmEntry";

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback'),
            'callbackBody' => '{
                "uuid"          : $(uuid),
                "endUser"       : $(endUser),
                "persistentId"  : $(persistentId),
                "bucket"        : $(bucket),
                "key"           : $(key),
                "etag"          : $(etag),
                "fsize"         : $(fsize),
                "mimeType"      : $(mimeType),
                "imageAve"      : $(imageAve),
                "ext"           : $(ext),
                "exif"          : $(exif),
                "imageInfo"     : $(imageInfo),
                "avinfo"        : $(avinfo)
            }',
            'callbackBodyType' => 'application/json',

            'persistentOps' => "$imageMogr2WebpFop",
            'persistentPipeline' => $qiniu->pipeline,
            'persistentNotifyUrl' => $qiniu->notify_url,
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        //上传文件
        $res = $qiniu->putFile($token,$key,$filepath,$params);

        return $this->response->array($res);
    }

    //生成视频上传凭证
    public function videoToken($mimeType = 'video/*',QiniuCloudHandler $qiniu){
        $prefix = 'video/';
        $newType = 'mp4';

        $key = $qiniu->makeFileNameByTime($prefix,$newType);
        $entry = s($qiniu->bucket . ":" . $key);

        //命令
        $persistentOps = "avthumb/{$newType}/wmText/".s('Dozhan')."/wmFontSize/40/wmFontColor/".s('#ffffff'). "/wmGravityText/NorthWest"."|saveas/{$entry}";//

        //上传策略
        $policy = $qiniu->makeUploadPolicy($key,$persistentOps,$mimeType);

        //上传token
        return $qiniu->makeUploadToken($key,$policy);
    }

    //生成图片上传凭证
    public function imageToken($scene,$mimeType = 'image/*',QiniuCloudHandler $qiniu){
        $prefix = "image/$scene/";
        $newType = 'webp';

        $key = $qiniu->makeFileNameByTime($prefix,$newType);
        $entry = s($qiniu->bucket . ":" . $key);

        $size = $this->getStandardImageSize($scene);
        //命令
        $persistentOps = "imageMogr2/auto-orient/thumbnail/{$size}!/format/{$newType}"."|saveas/{$entry}";

        //上传策略
        $policy = $qiniu->makeUploadPolicy($key,$persistentOps,$mimeType);

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
