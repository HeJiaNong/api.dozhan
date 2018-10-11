<?php

namespace App\Http\Controllers;

use App\Handlers\QiniuCloudHandler;
use Illuminate\Http\Request;
use function Qiniu\base64_urlSafeDecode;
use function Qiniu\base64_urlSafeEncode;

class UploadController extends Controller
{
    //测试方法
    public function index(QiniuCloudHandler $qiniu){

        //删除文件
//        $res = $qiniu->deleteFiles(["2018-10-10 22:21:15.mp4"]);dd($res);

        //查询文件信息
//        $res = $qiniu->getFileInfo("2018-10-10 22:21:15.mp4","dozhan");dd($res);

        //修改文件生存时间
//        $res = $qiniu->setDeleteAfterDays(1,"2018-10-10 22:21:15.mp4","dozhan");dd($res);

        dump('test_function');
    }

    //上传视频
    public function videoUpload(Request $request,QiniuCloudHandler $qiniu)
    {
        //表单验证
        $this->validate($request,[
            'file' => 'required|mimetypes:video/*'  //只能上传视频
        ]);

        //接收文件
        $file = $request->file('file');


        //预转处理
        $persistentOps = implode('',[
            //转码 MP4
            'avthumb/mp4',
            //水印文字
            '/wmText/'.base64_urlSafeEncode('Dozhan.cn'),
            //水印大小
            '/wmFontSize/40',
            //水印颜色
            '/wmFontColor/'.base64_urlSafeEncode('#ffffff'),
            //水印位置 - 左上
            '/wmGravityText/NorthWest',
        ]);

        //上传视频
        $res = $qiniu->uploadFile($file,$persistentOps);

        dump($res);

    }

    //上传图片
    public function imageUpload(Request $request,QiniuCloudHandler $qiniu){
        $this->validate($request,[
            'file' => 'required|mimetypes:image/*'
        ]);

        //接收文件
        $file = $request->file('file');

        //生成名称
        $key = '你好啊';

        //生成签名
        $saveas = $qiniu->makeSaveasUrl($key,'hicoder');

        //持久化处理指令列表
        $persistentOps = 'imageMogr2/thumbnail/576x360!/format/webp'."|saveas/{$saveas}";

        //上传
        $res = $qiniu->uploadFile($file,$persistentOps);

        dd($res);
    }
}
