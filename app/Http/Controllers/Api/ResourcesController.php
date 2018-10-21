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
