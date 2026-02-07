<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ZipSearchAsset extends AssetBundle
{
    public $sourcePath = null;
    public $js = [
        'js/zip-search.js',
    ];
    public $depends = [
        JuiAsset::class,
    ];
}
