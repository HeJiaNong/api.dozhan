<?php

namespace App\Http\Controllers\Api;

use App\Handlers\QiniuCloudHandler;
use App\Http\Requests\Api\ResourceRequest;
use App\Models\Resource;
use App\Models\ResourceQiniuPersistent;
use App\Models\ResourceQiniu;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function Qiniu\base64_urlSafeEncode;
use Webpatser\Uuid\Uuid;

class ResourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth')->except(['qiniuCallback','notification','uploadOfSeeder']);
    }

    /*
     * 七牛文件上传回调地址
     */
    public function qiniuCallback(ResourceRequest $request,ResourceQiniu $resourceQiniu, QiniuCloudHandler $handler,Resource $resource){
        Log::info('=======================七牛回调=======================');

        //验证回调是否是七牛发送
        if (!$handler->verifyCallback($request->all())){
            return $this->response->errorBadRequest();
        }
        Log::info('校验成功');

        $id = (string)Uuid::generate(4);

        //入库
        $resource->id = $id;
        $resource->user_id = $request->endUser;
        $resource->mime = $request->mimeType;
        $resourceQiniu->fill($request->all())->save();
        $resourceQiniu->resource()->save($resource);    //写入中间表

        Log::info('入库成功');

        Log::info('=======================七牛回调=======================');

        //返回响应
        return $this->response->array(compact('id'))->setStatusCode(200);
    }

    /*
     * 七牛持久化处理状态通知回调地址
     */
    public function notification(ResourceRequest $request, ResourceQiniuPersistent $qiniuPersistent){
        Log::info('=======================七牛持久化处理状态通知=======================');
        //验证id必须存在与七牛资源表中并且在七牛持久化处理资源表中唯一
        Validator::make($request->all(), [
            'id' => 'exists:resources_qiniu,persistentId|unique:resources_qiniu_persistent,id'
        ])->validate();

        //入库
        $qiniuPersistent->fill($request->all())->save();

        Log::info('=======================七牛持久化处理状态通知=======================');

        return $this->response->noContent();
    }

    /*
     * 上传文件token
     */
    public function token(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //todo 这里是否允许自定义文件名
        //文件名
        $key = utf8_encode('other/'.uniqid());

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => $qiniu->policy['callbackUrl'],
            'callbackBody' => '{
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
            'callbackBodyType' => $qiniu->policy['callbackBodyType'],
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return compact('key','token');
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
        $key = utf8_encode('video/'.uniqid());

        //转码MP4+水印 文件名
        $ops1SaveName = base64_urlSafeEncode("{$qiniu->bucket}:{$key}");
        //转码HLS+水印 文件名
        $ops2SaveName = base64_urlSafeEncode("{$qiniu->bucket}:{$key}(hls)");
        //切片ts文件名称前缀
        $hlsName = base64_urlSafeEncode("{$key}(hls-$(count))");

        //水印路径
        $wmUrl = base64_urlSafeEncode(config('services.qiniu.video.watermarkUrl'));
        //水印位置
        $Gravity = 'NorthWest';
        //水印指令
        $wm = "wmImage/{$wmUrl}/wmGravity/{$Gravity}";

        //转码MP4+水印指令
        $ops1 = "avthumb/mp4/{$wm}|saveas/{$ops1SaveName}";

        //转码HLS+水印指令
        $ops2 = "avthumb/m3u8/noDomain/1/segtime/20/vb/5m/pattern/{$hlsName}/r/60/{$wm}|saveas/{$ops2SaveName}";

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => $qiniu->policy['callbackUrl'],
            'callbackBody' => '{
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
            'callbackBodyType' => $qiniu->policy['callbackBodyType'],

            'persistentOps' => "$ops1;$ops2",
            'persistentPipeline' => $qiniu->policy['persistentPipeline'],
            'persistentNotifyUrl' => $qiniu->policy['persistentNotifyUrl'],

            'mimeLimit' => 'video/*',
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return $this->response->array(compact('key','token'));
    }

    /*
     * 图片token
     */
    public function imageToken($scene,QiniuCloudHandler $qiniu){
        $thumbnail = '/thumbnail/'.$this->getStandardImageSize($scene).'!';

        //文件名
        $key = utf8_encode("image/{$scene}/".uniqid());

        //水印文件另存编码
        $ops1SaveName = base64_urlSafeEncode("{$qiniu->bucket}:{$key}");

        //转码webp+缩放
        $ops1 = "imageMogr2/auto-orient{$thumbnail}/format/webp"."|saveas/{$ops1SaveName}";

        //上传策略
        $policy = [
            'endUser' => (string)$this->user->id,
            'callbackUrl' => $qiniu->policy['callbackUrl'],
            'callbackBody' => '{
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
            'callbackBodyType' => $qiniu->policy['callbackBodyType'],

            'persistentOps' => "$ops1",
            'persistentPipeline' => $qiniu->policy['persistentPipeline'],
            'persistentNotifyUrl' => $qiniu->policy['persistentNotifyUrl'],

            //限制上传文件类型
            'mimeLimit' => 'image/*',
        ];

        //生成上传凭证
        $token = $qiniu->uploadToken($qiniu->bucket,$key,$qiniu->expires,$policy);

        return compact('key','token');
    }

    /*
     * 表单上传文件
     */
    public function store(ResourceRequest $request,QiniuCloudHandler $qiniu,Resource $resource){
        //内部请求token接口
        $res = $this->api->be($this->user)->get('api/resources/token',['key' => $request->key]);

        $filepath = $request->file('file')->getRealPath();

        //上传文件
        list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath);

        if ($err){
            return $this->response->errorForbidden();
        }

        return $this->response->array($ret);
    }

    /*
     * 表单上传视频
     */
    public function video(ResourceRequest $request,QiniuCloudHandler $qiniu){
        //内部请求视频token接口，获取视频上传token
        $res = $this->api->be($this->user)->get('api/resources/video/token');

        $filepath = $request->file('video')->getRealPath();

        //上传文件
        list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath);

        if ($err){
            return $this->response->errorForbidden();
        }

        return $this->response->array($ret);
    }

    /*
     * 表单上传图片
     */
    public function image($scene,ResourceRequest $request,QiniuCloudHandler $qiniu){
        //文件本地路径
        $filepath = $request->file('image')->getRealPath();

        //获取token
        $res = $this->api->be($this->user)->get("api/resources/image/token/{$scene}");

        //上传文件
        list($ret,$err) = $qiniu->putFile($res['token'],$res['key'],$filepath);

        if ($err){
            return $this->response->errorForbidden();
        }

        return $this->response->array($ret);
    }

    /*
     * 获取场景对应图片尺寸
     */
    protected function getStandardImageSize($scene){
        $sizes = config('services.qiniu.image.size');
        foreach ($sizes as $sce => $siz){
            if ($scene == $sce){
                return $siz;
            }
        }
        abort(422);
    }

    //todo 这里需要一个专门为seeder准备的方法，用于上传文件，如果上传的文件已经存在，则读取文件信息，存入数据库
    public function uploadOfSeeder(ResourceRequest $request,QiniuCloudHandler $handler,ResourceQiniu $qiniu,Resource $resource){
        $file = $request->file('file')->getRealPath();
        if ($info = $handler->fileInfo($handler->bucket,$file)[0]){
            //入库，上传成功
            $qiniu->bucket = $handler->bucket;
            $qiniu->key = $file;
            $qiniu->endUser = $info['endUser'];
            $qiniu->fsize = $info['fsize'];
            $qiniu->etag = $info['hash'];
            $qiniu->mimeType = $info['mimeType'];
//            $qiniu->type = $info['type'];
            $qiniu->save();
            $resource->id = Uuid::generate(4);
            $resource->user_id = 1;
            $resource->mime = $info['mimeType'];
            $qiniu->resource()->save($resource);
        }else{
            //上传策略
            $policy = [
                'endUser' => '1',
                'callbackUrl' => $handler->policy['callbackUrl'],
                'callbackBody' => '{
                    "params"        : $(x:params),
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
                'callbackBodyType' => $handler->policy['callbackBodyType'],
            ];

            //生成上传凭证
            $token = $handler->uploadToken($handler->bucket,$file,$handler->expires,$policy);

            //上传参数
            $putExtra = [
                //自定义变量
                'params' => [
                    'x:params' => json_encode([
                        'user_id' => 1,
                    ]),
                ],
            ];
            list($ret,$err) = $handler->putFile($token,$file,$file,$putExtra['params']);

            if ($err){
                return $this->response->errorForbidden();
            }

            return $this->response->array($ret);
        }
    }
}
