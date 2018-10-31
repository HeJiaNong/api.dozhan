<?php

namespace App\Handlers;

use Qiniu\Auth;
use function Qiniu\base64_urlSafeEncode as s;
use Qiniu\Config;
use Qiniu\Processing\PersistentFop;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;

/*
 * 七牛云服务封装：
 * 上传文件
 * 音视频处理
 * 图片处理
 */
class QiniuCloudHandler
{
    private $access_key;    //AK
    private $secret_key;    //SK
    public $domain; //你的七牛域名
    public $bucket;        //对象存储空间名
    public $expires;       //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    public $pipeline;      //队列名
    public $notify_url;    //持久化处理回调地址

    public function __construct()
    {
        //初始化配置
        $this->buildConfig();
    }

    /**
     * 初始化配置信息
     */
    private function buildConfig(){
        $this->domain      = \config('services.qiniu.domain');
        $this->bucket      = \config('services.qiniu.bucket');
        $this->access_key  = \config('services.qiniu.access_key');
        $this->secret_key  = \config('services.qiniu.secret_key');
        $this->expires     = \config('services.qiniu.expires');
        $this->pipeline    = \config('services.qiniu.pipeline');
        $this->notify_url  = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('api.resource.notification');
    }

    /**
     * 上传文件到七牛
     *
     * @param string $upToken    上传凭证
     * @param string $key        上传到七牛后保存的文件名
     * @param string $filePath   要上传文件的本地路径
     * @param string $params     自定义变量，规格参考
     *                    http://developer.qiniu.com/docs/v6/api/overview/up/response/vars.html#xvar
     * @param string $mime       上传数据的mimeType
     * @param bool $checkCrc     是否校验crc32
     *
     * @return array    包含已上传文件的信息，类似：
     *                                              [
     *                                                  "hash" => "<Hash string>",
     *                                                  "key" => "<Key string>"
     *                                              ]
     */
    public function putFile($upToken,$key,$filePath,$params = null,$mime = 'application/octet-stream',$checkCrc = false)
    {
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        $uploader = new UploadManager();
        $res = $uploader->putFile($upToken,$key,$filePath,$params,$mime,$checkCrc);

        dd($res);
        //解析后，返回上传结果
        return $this->parseRes($res);
    }

    /*
     * 生成上传凭证
     */
    public function uploadToken($bucket, $key = null, $expires = 3600, $policy = null, $strictPolicy = true){
        $auth = new Auth($this->access_key, $this->secret_key);

        $token = $auth->uploadToken($bucket, $key, $expires, $policy, $strictPolicy);

        return $token;
    }

    /*
     * 生成上传策略
     *
     * 上传策略是资源上传时附带的一组配置设定。通过这组配置信息，七牛云存储可以了解用户上传的需求：它将上传什么资源，
     * 上传到哪个空间，上传结果是回调通知还是使用重定向跳转，是否需要设置反馈信息的内容，以及授权上传的截止时间等等
     */
    public function uploadPolicy(){
        //
    }



    //生成上传策略
    public function makeUploadPolicy($key,$ops,$mimeType){
        return [
            //==========================================================================================================
            //<bucket>//<bucket>:<keyPrefix>//<bucket>:<key>=表示只允许用户上传指定 key 的文件。在这种格式下文件默认允许修改，若已存在同名资源则会被覆盖。
            "scope"                 => "{$this->bucket}:{$key}",    //"{$this->bucket}:{$key}",
            //若为 1，表示允许用户上传以 scope 的 keyPrefix 为前缀的文件。
            //"isPrefixalScope"       => 0,
            //上传凭证有效截止时间。Unix时间戳，单位为秒。该截止时间为上传完成后，在七牛空间生成文件的校验时间，而非上传的开始时间，一般建议设置为上传开始时间 + 3600s，用户可根据具体的业务场景对凭证截止时间进行调整。
            "deadline"              => $this->expires,
            //限定为新增语意。如果设置为非 0 值，则无论 scope 设置为什么形式，仅能以新增模式上传文件。
            //"insertOnly"            => 0,
            //==========================================================================================================

            //==========================================================================================================
            //唯一属主标识。特殊场景下非常有用，例如根据 App-Client 标识给图片或视频打水印。
            //"endUser"               => "<EndUserId                string>",
            //==========================================================================================================

            //==========================================================================================================
            //Web 端文件上传成功后，浏览器执行 303 跳转的 URL。通常用于表单上传。文件上传成功后会跳转到 <returnUrl>?upload_ret=<queryString>，<queryString>包含 returnBody 内容。如不设置 returnUrl，则直接将 returnBody 的内容返回给客户端。
            //"returnUrl"             => "<RedirectURL              string>",
            //上传成功后，自定义七牛云最终返回給上传端（在指定 returnUrl 时是携带在跳转路径参数中）的数据。支持魔法变量和自定义变量。returnBody 要求是合法的 JSON 文本。例如 {"key": $(key), "hash": $(etag), "w": $(imageInfo.width), "h": $(imageInfo.height)}。
            "returnBody"            => '{"bucket":"$(bucket)","key":"$(key)","mimeType":$(mimeType),"hash":"$(etag)","persistentId":"$(persistentId)"}',
            //==========================================================================================================

            //==========================================================================================================
            //上传成功后，七牛云向业务服务器发送 POST 请求的 URL。必须是公网上可以正常进行 POST 请求并能响应 HTTP/1.1 200 OK 的有效 URL。另外，为了给客户端有一致的体验，我们要求 callbackUrl 返回包 Content-Type 为 "application/json"，即返回的内容必须是合法的 JSON 文本。出于高可用的考虑，本字段允许设置多个 callbackUrl（用英文符号 ; 分隔），在前一个 callbackUrl 请求失败的时候会依次重试下一个 callbackUrl。一个典型例子是：http://<ip1>/callback;http://<ip2>/callback，并同时指定下面的 callbackHost 字段。在 callbackUrl 中使用 ip 的好处是减少对 dns 解析的依赖，可改善回调的性能和稳定性。指定 callbackUrl，必须指定 callbackbody，且值不能为空。
            //"callbackUrl"           => $this->notify_url,
            //上传成功后，七牛云向业务服务器发送回调通知时的 Host 值。与 callbackUrl 配合使用，仅当设置了 callbackUrl 时才有效。
            //"callbackHost"          => "<RequestHostForAppServer  string>",
            //上传成功后，七牛云向业务服务器发送 Content-Type: application/x-www-form-urlencoded 的 POST 请求。业务服务器可以通过直接读取请求的 query 来获得该字段，支持魔法变量和自定义变量。callbackBody 要求是合法的 url query string。例如key=$(key)&hash=$(etag)&w=$(imageInfo.width)&h=$(imageInfo.height)。如果callbackBodyType指定为application/json，则callbackBody应为json格式，例如:{"key":"$(key)","hash":"$(etag)","w":"$(imageInfo.width)","h":"$(imageInfo.height)"}。
            //"callbackBody"          => '{"bucket":"$(bucket)","key":"$(key)","mimeType":$(mimeType),"hash":"$(etag)","persistentId":"$(persistentId)"}',
            //上传成功后，七牛云向业务服务器发送回调通知 callbackBody 的 Content-Type
            //"callbackBodyType"      => "application/json",
            //==========================================================================================================

            //==========================================================================================================
            //资源上传成功后触发执行的预转持久化处理指令列表。支持魔法变量和自定义变量。每个指令是一个 API 规格字符串，多个指令用;分隔。
            "persistentOps"         => $ops,
            //接收持久化处理结果通知的 URL。必须是公网上可以正常进行 POST 请求并能响应 HTTP/1.1 200 OK 的有效 URL。该 URL 获取的内容和持久化处理状态查询的处理结果一致。发送 body 格式是 Content-Type 为 application/json 的 POST 请求，需要按照读取流的形式读取请求的 body 才能获取。
            "persistentNotifyUrl"   => $this->notify_url,
            //转码队列名。资源上传成功后，触发转码时指定独立的队列进行转码。为空则表示使用公用队列，处理速度比较慢。建议使用专用队列。
            "persistentPipeline"    => $this->pipeline,
            //自定义资源名。支持魔法变量和自定义变量。这个字段仅当用户上传的时候没有主动指定 key 的时候起作用。
            //"saveKey"               => $key,
            //限定上传文件大小最小值，单位Byte。
            //"fsizeMin"              => "<FileSizeMin              int64>",
            //限定上传文件大小最大值，单位Byte。超过限制上传文件大小的最大值会被判为上传失败，返回 413 状态码。
            //"fsizeLimit"            => 250000000,
            //==========================================================================================================

            //==========================================================================================================
            //开启 MimeType 侦测功能。设为非 0 值，则忽略上传端传递的文件 MimeType 信息，使用七牛服务器侦测内容后的判断结果。
            "detectMime"            => 3,
            //==========================================================================================================

            //==========================================================================================================
            //限定用户上传的文件类型。指定本字段值，七牛服务器会侦测文件内容以判断 MimeType，再用判断值跟指定值进行匹配，匹配成功则允许上传，匹配失败则返回 403 状态码。
            "mimeLimit"             => $mimeType,
            //文件存储类型。0 为普通存储（默认），1 为低频存储。
            "fileType"              => 0,
            //==========================================================================================================
        ];
    }

    /**
     * 获取文件信息
     * @param $key string 空间文件名
     * @param $bucket string 空间名
     * @return mixed
     */
    public function getFileInfo($key,$bucket){
        $res = $this->getBucketManagerObject()->stat($bucket, $key);

        return $this->parseRes($res);
    }

    /**
     * 设置或更新文件的生存时间
     * @param $days integer 天数
     * @param $key string 文件名
     * @param $bucket string 空间名
     */
    public function setDeleteAfterDays($days,$key,$bucket){
        //如果 $err 不为空，代表操作失败或发生错误
        $err = $this->getBucketManagerObject()->deleteAfterDays($bucket, $key, $days);

        return $err;
    }

    /**
     * 获取 空间管理 对象
     * @return BucketManager
     */
    private function getBucketManagerObject(){
        return new BucketManager($this->auth, new Config());
    }

    /**
     * 获取另存的签名
     * @param string $key 空间文件名
     * @param string $bucket 空间名
     * @return string
     */
    public function makeSaveasUrl(string $key,string $bucket = ''){
        return s((empty($bucket)?$this->bucket:$bucket).':'.$key);
    }

    /**
     * 通过当前时间生成名字
     * @param $file
     * @param $prefix string 前缀
     * @return string
     */
    public function makeFileNameByTime(string $prefix = '',string $ext = ''){
        return str_replace(' ','-',$prefix . now()->toDateTimeString() .'-'. uniqid() .'.'.$ext);
    }

    /**
     * EncodedEntryURI 格式,本格式用于在 URI中指定目标资源空间与目标资源名
     * @param $key string 空间文件名
     * @return string
     */
    public function getEncodedEntryURI($key){
        return s($this->bucket.':'.$key);
    }

    /**
     * 获取持久化工作状态
     * @param $persistentId string 任务ID
     * @return array
     */
    public function getCloudHandleStatus($persistentId){
        //
        $pfop = new PersistentFop($this->auth,new Config());

        //查询转码的进度和状态
        $res = $pfop->status($persistentId);

        return $res;
    }

    /**解析返回状态码
     * @param $res array 七牛返回的数据
     * @return mixed
     */
    private function parseRes($res){

        // 列举文件
        list($ret, $err) = $res;
        //状态判断
        if ($err !== null) {
            return false;
        } else {
            return $ret;
        }

    }

    /**
     * 批量删除文件
     * @param $keys
     * @param string $bucket
     */
    public function deleteFiles(array $keys,string $bucket = ''){

        $bucket = empty($bucket)?$this->bucket:$bucket;

        //每次最多不能超过1000个
        if (count($keys) > 1000){
            return false;
        }

        $ops = $this->getBucketManagerObject()->buildBatchDelete($bucket, $keys);

        $res = $this->getBucketManagerObject()->batch($ops);

        return $this->parseRes($res);
    }

    /**
     * 拼接空间文件的url链接
     * @param $key string 空间文件名
     * @param string $domain 域名
     * @return string
     */
    public function getFileUrl($key,$domain = ''){
        $domain = empty($domain)?$this->qiniuDomain:$domain;
        return $domain . '/' . $key;
    }

}