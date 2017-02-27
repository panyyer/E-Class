<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'statics/css/jquery.popup.min.css',

    ];

    public $js = [
        'statics/js/jquery.popup.min.js',
        'statics/js/jquery.popup.dialog.min.js',

    ];
    public $depends = [
        
    ];
}
