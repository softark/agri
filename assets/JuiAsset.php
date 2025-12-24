<?php

namespace app\assets;

use yii\web\AssetBundle;

class JuiAsset extends AssetBundle
{
    public $sourcePath = '@bower/jquery-ui';
    public $js = [
        'jquery-ui.min.js',
    ];
    public $css = [
        'https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}