<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'timeZone'=>'Asia/Shanghai',    //设置时间戳，将来改为英国时区
];
