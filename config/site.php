<?php

return [
    //banner
    'banners' => [
        [
            'img' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/1.jpg',
            'link' => '#',
            'desc' => 'welcome',
        ],
        [
            'img' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/2.jpg',
            'link' => '#',
            'desc' => 'dozhan',
        ],
        [
            'img' => 'http://phcczptg4.bkt.clouddn.com/seeder/banner/3.jpg',
            'link' => '#',
            'desc' => '嘿嘿',
        ],
    ],

    //友情链接
    'links' => [
        '百度' => 'http://www.baidu.com',
        '新浪' => 'http://www.sina.com',
    ],

    //消息通知
    'notifications' => [
        'Comment' => [
            'via' => ['database','mail']
        ],
        'Favour' => [
            'via' => ['database','mail']
        ],
        'Follow' => [
            'via' => ['database','mail']
        ],
    ],
];