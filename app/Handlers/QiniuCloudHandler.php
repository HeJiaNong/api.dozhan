<?php

namespace App\Handlers;

use Qiniu\Auth;
use function Qiniu\base64_urlSafeEncode;
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
    public $qiniuDomain; //你的七牛域名
    public $bucket;        //对象存储空间名
    private $access_key;    //AK
    private $secret_key;    //SK
    private $expires;       //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    public $pipeline;      //队列名
    public $notify_url;    //持久化处理回调地址
    private $auth;          //鉴权对象Auth

    public function __construct()
    {
        //初始化配置
        $this->build();
    }

    /**
     * 初始化配置信息
     */
    private function build(){
        $this->qiniuDomain = \config('qiniu.qiniuDomain');
        $this->bucket      = \config('qiniu.bucket');
        $this->access_key  = \config('qiniu.access_key');
        $this->secret_key  = \config('qiniu.secret_key');
        $this->expires     = \config('qiniu.expires');
        $this->pipeline    = \config('qiniu.pipeline');
        $this->notify_url  = \config('qiniu.notify_url');
        $this->auth        = new Auth($this->access_key, $this->secret_key);   //定义鉴权对象Auth
    }

    /**
     * 上传文件
     * @param $file resource 文件对象
     * @param string $persistentOps 上传策略
     * @param string $filename 自定义上传后的名称
     * @return mixed
     * @throws \Exception
     */
    public function uploadFile(string $filePath,string $filename,$persistentOps = '',string $bucket = '')
    {
        //获取空间名
        $bucket = empty($bucket) ? $this->bucket : $bucket;
        //
        $policy = [
            'scope' => $bucket,
            'deadline' => $this->expires,

            //持久化处理
            'persistentOps' => $persistentOps,          //持久化处理指令列表
            'persistentNotifyUrl' => $this->notify_url, //接收持久化处理结果通知的 URL
            'persistentPipeline' => $this->pipeline,    //转码队列名

            //自定义返回内容
            'returnBody' => '{"bucket":"$(bucket)","key":"$(key)","mimeType":$(mimeType),"hash":"$(etag)","persistentId":"$(persistentId)"}',
        ];

        dd($this->auth->uploadToken($bucket,null,$this->expires,$policy,true));

        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        $res = (new UploadManager())->putFile(
            //生成上传凭证
            $this->auth->uploadToken($bucket,null,$this->expires,$policy,true),
            //上传至七牛的文件名
            $filename,
            //本地文件的路径
            $filePath
        );

        //解析后，返回上传结果
        return $this->parseRes($res);
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
        return base64_urlSafeEncode((empty($bucket)?$this->bucket:$bucket).':'.$key);
    }

    /**
     * 通过当前时间生成名字
     * @param $file
     * @param $prefix string 前缀
     * @return string
     */
    public function makeFileNameByTime(string $prefix = '',string $ext = ''){
        return str_replace(' ','-',$prefix . now()->toDateTimeString().'.'.$ext);
    }

    /**
     * EncodedEntryURI 格式,本格式用于在 URI中指定目标资源空间与目标资源名
     * @param $key string 空间文件名
     * @return string
     */
    public function getEncodedEntryURI($key){
        return base64_urlSafeEncode($this->bucket.':'.$key);
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