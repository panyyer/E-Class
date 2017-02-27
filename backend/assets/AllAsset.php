<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AllAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'statics/css/bootstrap.min.css',
        'statics/css/bootstrap-responsive.min.css',
        'statics/css/font-awesome.css',
        'statics/css/adminia.css',
        'statics/css/adminia-responsive.css',
        'statics/css/pages/login.css',
    ];
    public $jsOptions = [
        'condition' => 'lt IE9',
        'position' => \yii\web\View::POS_HEAD  //放在head里
    ];
    public $js = [
        'http://html5shim.googlecode.com/svn/trunk/html5.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
