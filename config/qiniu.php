<?php

return [
    //七牛默认域名
    'qiniuDomain'   => 'pglgpkuzs.bkt.clouddn.com',
    //对象存储空间默认名
    'bucket'        => 'dozhan-testing',
    //AK
    'access_key'    => '7jLUeoq1Un9H5fJjTid-kNwva3x2uAASEsD3DXyd',
    //SK
    'secret_key'    => '07HYzerzkxElZyPgnShHn9luffNPNuKwegpE16oY',
    //自定义凭证有效期（expires单位为秒，为上传凭证的有效时间）
    'expires'       => time()+3600,
    //队列名
    'pipeline'      => 'dozhan',
    //持久化处理回调地址
    'notify_url'    => 'http://www.baidu.com',

];
