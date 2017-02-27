<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class ScoreAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD   //放在头部
    ];
    public $js = [
        'statics/js/chart.js',   //平时分的图表
    ];
    public $depends = [

    ];
}
