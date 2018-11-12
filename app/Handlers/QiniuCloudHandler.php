<?php

namespace App\Handlers;

use App\Models\ResourceQiniu;
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
    public $domain;         //空间域名
    public $bucket;         //对象存储空间名
    public $expires;        //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    public $policy = [];    //上传policy
    public $auth;

    public function __construct()
    {
        //初始化配置
        $this->buildConfig();
    }

    /**
     * 初始化配置信息
     */
    private function buildConfig(){
        $this->domain       = \config('services.qiniu.domain');
        $this->bucket       = \config('services.qiniu.bucket');
        $this->access_key   = \config('services.qiniu.access_key');
        $this->secret_key   = \config('services.qiniu.secret_key');
        $this->expires      = \config('services.qiniu.expires');

        $this->policy['callbackUrl'] = \config('services.qiniu.policy.callbackUrl')();
        $this->policy['callbackBodyType'] = \config('services.qiniu.policy.callbackBodyType');
        $this->policy['persistentPipeline'] = \config('services.qiniu.policy.persistentPipeline');
        $this->policy['persistentNotifyUrl'] = \config('services.qiniu.policy.persistentNotifyUrl')();

        $this->auth         = new Auth($this->access_key, $this->secret_key);
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

        return $res;
    }

    /*
     * 生成上传凭证
     */
    public function uploadToken($bucket, $key = null, $expires = 3600, $policy = null, $strictPolicy = true){
        //todo 这里做一下上传策略验证，配置文件中的策略值大于自己配置的值
        $token = $this->auth->uploadToken($bucket, $key, $expires, $policy, $strictPolicy);

        return $token;
    }

    /**
     * 验证回调是否来自七牛
     * @param $data
     */
    public function verifyCallback($data){
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])){
            return false;
        }

        return $this->auth->verifyCallback($this->policy['callbackBodyType'], $_SERVER['HTTP_AUTHORIZATION'], $this->policy['callbackUrl'],$data);
    }


    /**
     * 获取文件信息
     * @param $key string 空间文件名
     * @param $bucket string 空间名
     * @return mixed
     */
    public function fileInfo($bucket,$key){

        $res = $this->getBucketManagerObject()->stat($bucket,$key);

        return $res;
    }

    //================================================


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
    public function parseRes($res){

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

}