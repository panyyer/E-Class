<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class CommonAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    	'statics/css/style.css'
    ];

    public $js = [
        'statics/js/bootstrap.js',
        'statics/js/script.js',
    ];
    public $depends = [

    ];
}
