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
    public function qiniuCallbak(Request $request,QiniuCloudHandler $handler){
        logger('================七牛CallbackURL================');
        logger($request->all());
        logger('================七牛CallbackURL================');
        $url = "http://$handler->domain/$request->key";
        return $this->response->array(compact('url'))->header('Content-Type','application/json');
        /*
  'url' => '{"original":"http:\\/\\/phcczptg4.bkt.clouddn.com\\/5bd9c6fb3bc3e"}',
  'bucket' => 'dozhan',
  'key' => '5bd9c6fb3bc3e',
  'etag' => 'FqgLGY7h2wu-gcQ8GX1o0GBouL-p',
  'fsize' => 2378354,
  'mimeType' => 'video/mp4',
  'endUser' => NULL,
  'persistentId' => 'z2.5bd9c70ce3d00409792e3f80',
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
        $params = [
//            'x:url' => json_encode(["original" => "http://$qiniu->domain/$key"]),
//            'x:user_id' => $this->user->id,
        ];

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
                "url"           : $(x:url),
                "bucket"        : $(bucket),
                "key"           : $(key),
                "etag"          : $(etag),
                "fsize"         : $(fsize),
                "mimeType"      : $(mimeType),
                "endUser"       : $(endUser),
                "user_id"       : $(x:user_id),
                "persistentId"  : $(persistentId),
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

        $scene = $request->scene;

        $mimeType = 'image/*';

        list($key,$token) = $this->imageToken($scene,$mimeType,$qiniu);
//        dd($request->file('image')->getMimeType());
        $res = $qiniu->uploadFile($request->file('image')->getRealPath(),$key,$token);

        dd($res);
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

    //七牛持久化处理接口通知回调地址
    public function notification(Request $request){

        logger('七牛处理回调地址>>>>>>>>>>>>>>>>>>>>');
        logger($request->all());
        logger('七牛处理回调地址<<<<<<<<<<<<<<<<<<<<');
//图片
//[2018-10-20 17:05:23] testing.DEBUG: 七牛处理回调地址>>>>>>>>>>>>>>>>>>>>
//[2018-10-20 17:05:23] testing.DEBUG: array (
//    'id' => 'z2.5bcaefd0e3d0040979f7c756',
//    'pipeline' => '1381556928.dozhan',
//    'code' => 0,
//    'desc' => 'The fop was completed successfully',
//    'reqid' => 'HG0AAAKbB8_HRV8V',
//    'inputBucket' => 'dozhan-testing',
//    'inputKey' => 'video/2018-10-20-17:05:20-5bcaefd026e0d.webp',
//    'items' =>
//        array (
//            0 =>
//                array (
//                    'cmd' => 'imageMogr2/auto-orient/thumbnail/200x200!/format/webp|saveas/ZG96aGFuLXRlc3Rpbmc6dmlkZW8vMjAxOC0xMC0yMC0xNzowNToyMC01YmNhZWZkMDI2ZTBkLndlYnA=',
//                    'code' => 0,
//                    'desc' => 'The fop was completed successfully',
//                    'hash' => 'FpWo79X2867BjOhP9lo2KllOpjQz',
//                    'key' => 'video/2018-10-20-17:05:20-5bcaefd026e0d.webp',
//                    'returnOld' => 0,
//                ),
//        ),
//)
//[2018-10-20 17:05:23] testing.DEBUG: 七牛处理回调地址<<<<<<<<<<<<<<<<<<<<
//视频
//[2018-10-20 17:07:35] testing.DEBUG: 七牛处理回调地址>>>>>>>>>>>>>>>>>>>>
//[2018-10-20 17:07:35] testing.DEBUG: array (
//    'id' => 'z2.5bcaf053e3d0040979f7c881',
//    'pipeline' => '1381556928.dozhan',
//    'code' => 0,
//    'desc' => 'The fop was completed successfully',
//    'reqid' => 'fCQAAEa-tSfmRV8V',
//    'inputBucket' => 'dozhan-testing',
//    'inputKey' => 'video/2018-10-20-17:07:14-5bcaf0421e197.mp4',
//    'items' =>
//        array (
//            0 =>
//                array (
//                    'cmd' => 'avthumb/mp4/wmText/RG96aGFu/wmFontSize/40/wmFontColor/I2ZmZmZmZg==/wmGravityText/NorthWest|saveas/ZG96aGFuLXRlc3Rpbmc6dmlkZW8vMjAxOC0xMC0yMC0xNzowNzoxNC01YmNhZjA0MjFlMTk3Lm1wNA==',
//                    'code' => 0,
//                    'desc' => 'The fop was completed successfully',
//                    'hash' => 'Fhu8guKbeQXTTrGyCrNelz-GRZSL',
//                    'key' => 'video/2018-10-20-17:07:14-5bcaf0421e197.mp4',
//                    'returnOld' => 0,
//                ),
//        ),
//)
//[2018-10-20 17:07:35] testing.DEBUG: 七牛处理回调地址<<<<<<<<<<<<<<<<<<<<
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
