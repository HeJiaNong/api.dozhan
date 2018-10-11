<?php

return [
    'QiniuDomain'   => 'qiniu.hjn.ink',                             //你的七牛域名
    'bucket'        => 'dozhan',                                    //对象存储空间名
    'access_key'    => '7jLUeoq1Un9H5fJjTid-kNwva3x2uAASEsD3DXyd',  //AK
    'secret_key'    => '07HYzerzkxElZyPgnShHn9luffNPNuKwegpE16oY',  //SK
    'expires'       => time()+3600,                                 //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    'pipeline'      => 'dozhan',                                    //队列名
    'notify_url'    => 'http://www.baidu.com',                      //持久化处理回调地址

];
