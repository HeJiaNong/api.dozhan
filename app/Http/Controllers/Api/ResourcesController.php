<?php

namespace App\Http\Controllers\Api;

use App\Handlers\QiniuCloudHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Http\Requests\Api\ResourceRequest;
use App\Http\Requests\Api\VideoRequest;
use App\Models\ResourceQiniuPersistent;
use App\Models\ResourceQiniu;
use App\Models\User;
use Carbon\Carbon;
use Dingo\Api\Routing\UrlGenerator;

use Illuminate\Support\Facades\Log;
use function Qiniu\base64_urlSafeEncode;
use Webpatser\Uuid\Uuid;

class ResourcesController extends Controller
{
    /*
     * 七牛文件上传回调地址
     */
    public function qiniuCallback(ResourceRequest $request, ResourceQiniu $qiniuResource, QiniuCloudHandler $handler){
        Log::info('>>>>>>>>>>>>>>>>>>>>七牛回调请求进入>>>>>>>>>>>>>>>>>>>>');

        //接收数据
        $data = $request->only(['params','id', 'endUser', 'persistentId', 'bucket', 'key', 'etag', 'fsize', 'mimeType', 'imageAve', 'ext', 'exif', 'imageInfo','avinfo']);

        //七牛回调的url，具体可以参考：http://developer.qiniu.com/docs/v6/api/reference/security/put-policy.html
        $url = app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback');

        if (!isset($_SERVER['HTTP_AUTHORIZATION'])){
            return $this->response->errorBadRequest();
        }

        //检测是否是七牛返回的回调
        $isQiniuCallback = $handler->auth->verifyCallback('application/json', $_SERVER['HTTP_AUTHORIZATION'], $url,$data);

        if ($isQiniuCallback){
            $id = $data['id'];
            //入库
            $qiniuResource->fill($data)->save();

            Log::info('<<<<<<<<<<<<<<<<<<<<七牛回调请求成功<<<<<<<<<<<<<<<<<<<<');

            //返回响应
            return $this->response->array(compact('id'))->setStatusCode(200);
        }else{
            return $this->response->errorBadRequest();
        }
    }

    /*
     * 七牛持久化处理状态通知回调地址
     */
    public function notification(ResourceRequest $request, ResourceQiniuPersistent $qiniuPersistent){
        Log::info('>>>>>>>>>>>>>>>>>>>>七牛持久化处理状态通知回调地址请求>>>>>>>>>>>>>>>>>>>>');
        //接收数据
        $data = $request->only(['id', 'pipeline', 'code', 'desc', 'reqid', 'inputBucket', 'inputKey', 'items']);

        //入库
        $qiniuPersistent->fill($data)->save();

        Log::info('<<<<<<<<<<<<<<<<<<<<七牛持久化处理状态通知回调地址成功<<<<<<<<<<<<<<<<<<<<');

        return $this->response->noContent();
    }

    /*
     * 上传文件token
     */
    public function token(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //文件名
        if ($request->key && !is_null($request->key)){
            $key = utf8_encode($request->key);
        }else{
            $key = utf8_encode(config('services.qiniu.upload.other.prefix').uniqid());
        }

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback'),
            'callbackBody' => '{
                "id"            : $(uuid),
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

            'mimeLimit' => \config('services.qiniu.upload.other.mimeType',null),
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return compact('key','token');
    }

    /*
     * 表单上传文件
     */
    public function store(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //内部请求视频token接口，获取视频上传token
        $res = $this->api->be($this->user)->get('api/resource/token',['key' => $request->key]);

        $filepath = $request->file('file')->getRealPath();

        if ($info = $qiniu->fileExists($qiniu->bucket,$res['key'])[0]){
            $resource = ResourceQiniu::where([['bucket','=',$qiniu->bucket],['key','=',$res['key']]])->first();
            //如果数据库有值
            if ($resource){
                $id = $resource->id;
                $res = compact('id');
            }else{
                $resource = [
                    'bucket' => $qiniu->bucket,
                    'key' => $res['key'],
                    'ext' => '',
                    'endUser' => $info['endUser'],
                    'fsize' => $info['fsize'],
                    'etag' => $info['hash'],
                    'mimeType' => $info['mimeType'],
                ];
                $resource = ResourceQiniu::create($resource);
                $id = $resource->id;
                $res = compact('id');
            }
        }else{
            //上传文件
            list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath);
//            dd($ret,$err);
            if ($err){
                return $this->response->errorForbidden();
            }
            $res = $ret;
        }

        return $this->response->array($res);
    }

    /*
     * 视频token
     */
    public function videoToken(QiniuCloudHandler $qiniu){
        /*
         * 前端上传文件参数列表：(file, key, token, putExtra, config)
         * file : Blob 对象，上传的文件
         * key:文件资源名
         * token:上传验证信息，前端通过接口请求后端获得
         * putExtra:{
         *      fname,  //文件原文件名
         *      params, //用来放置自定义变量
         *      mimiType    //用来限制上传文件类型，为 null 时表示不对文件类型限制
         * }
         * config:{
         *      useCdnDomain,   //表示是否使用 cdn 加速域名，为布尔值，true 表示使用，默认为 false。
         *      disableStatisticsReport,    //是否禁用日志报告，为布尔值，默认为false。
         *      region, //当为 null 或 undefined 时，自动分析上传域名区域
         *      retryCount, //上传自动重试次数（整体重试次数，而不是某个分片的重试次数）；默认 3 次（即上传失败后最多重试两次）；目前仅在上传过程中产生 599 内部错误时生效，但是未来很可能会扩展为支持更多的情况。
         *      concurrentRequestLimit,     //分片上传的并发请求量，number，默认为3；因为浏览器本身也会限制最大并发量，所以最大并发量与浏览器有关。
         *      checkByMD5, //是否开启 MD5 校验，为布尔值；在断点续传时，开启 MD5 校验会将已上传的分片与当前分片进行 MD5 值比对，若不一致，则重传该分片，避免使用错误的分片。读取分片内容并计算 MD5 需要花费一定的时间，因此会稍微增加断点续传时的耗时，默认为 false，不开启。
         * }
         */

        //文件名
        $key = utf8_encode(\config('services.qiniu.upload.video.prefix').uniqid());

        //转码MP4+水印 文件名
        $ops1SaveName = base64_urlSafeEncode($qiniu->bucket.':'.$key.'[mp4,wm]');
        //转码HLS+水印 文件名
        $ops2SaveName = base64_urlSafeEncode($qiniu->bucket . ':'.$key. '[hls,wm]');
        //切片ts文件名称前缀
        $hlsName = base64_urlSafeEncode($key."[hls,wm]"."($(count))");

        //水印文字
        $wmText = base64_urlSafeEncode('Dozhan');
        //水印颜色
        $wmColor = base64_urlSafeEncode('white');

        //转码MP4+水印指令
        $ops1 = "avthumb/mp4/wmText/$wmText/wmGravityText/NorthWest/wmFontColor/$wmColor/wmFontSize/50|saveas/$ops1SaveName";
        //转码HLS+水印指令
        $ops2 = "avthumb/m3u8/noDomain/1/segtime/20/vb/5m/pattern/$hlsName/r/60/wmText/$wmText/wmGravityText/NorthWest/wmFontColor/$wmColor/wmFontSize/50|saveas/$ops2SaveName";

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback'),
            'callbackBody' => '{
                "params"        : $(x:params),
                "id"            : $(uuid),
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

            'persistentOps' => "$ops1;$ops2",
            'persistentPipeline' => $qiniu->pipeline,
            'persistentNotifyUrl' => $qiniu->notify_url,

            'mimeLimit' => \config('services.qiniu.upload.video.mimeType',null),
        ];

        //前端需求的上传参数
        $putExtra = [
            //自定义变量
            'params' => [
                'x:params' => json_encode([
                    //todo 权重排序,此方案有待优化 eg:用tag来表示呢？
                    'wi' => [
                        $key. '[hls,wm]',
                        $key.'[mp4,wm]',
                    ],
                ]),
            ],
            'mimeType' => \config('services.qiniu.upload.video.mimeType',null),
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return $this->response->array(compact('key','token','putExtra'));
    }

    /*
     * 图片token
     */
    public function imageToken(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //文件名
        $key = utf8_encode(config('services.qiniu.upload.image.prefix').uniqid());

        //水印文件另存编码
        $saveWmEntry = base64_urlSafeEncode($qiniu->bucket . ":$key" . '[webp,thumbnail]');

        //转码webp+缩放
        $imageMogr2WebpFop = "imageMogr2/auto-orient/format/webp"."|saveas/$saveWmEntry";

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => app(UrlGenerator::class)->version('v1')->route('api.resources.qiniu.callback'),
            'callbackBody' => '{
                "params"        : $(x:params),
                "id"            : $(uuid),
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

            'mimeLimit' => \config('services.qiniu.upload.image.mimeType',null),
        ];

        //前端需求的上传参数
        $putExtra = [
            //自定义变量
            'params' => [
                'x:params' => json_encode([
                    //权重排序
                    'wi' => [
                        $key. '[webp,thumbnail]',
                    ],
                ]),
            ],
            'mimeType' => config('services.qiniu.upload.image.mimeType',null),
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return compact('key','token','putExtra');
    }

    /*
     * 表单上传视频
     */
    public function video(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //内部请求视频token接口，获取视频上传token
        $res = $this->api->be($this->user)->get('api/resource/video/token');

        $filepath = $request->file('video')->getRealPath();

        //上传文件
        list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath,$res['putExtra']['params']);

        if ($err){
            return $this->response->errorForbidden();
        }

        return $this->response->array($ret);
    }

    /*
     * 表单上传图片
     */
    public function image(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //文件本地路径
        $filepath = $request->file('image')->getRealPath();

        //获取token
        $res = $this->api->be($this->user)->get('api/resource/image/token',['scene' => $request->scene]);

        //上传文件
        list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath,$res['putExtra']['params']);

        if ($err){
            return $this->response->errorForbidden();
        }

        return $this->response->array($ret);
    }

    /*
     * 获取场景对应图片尺寸
     */
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
