<?php
namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class BootboxAsset extends AssetBundle
{
    public $sourcePath = '@bower/bootbox/dist';
    public $js = [
        'bootbox.min.js',
    ];

    public static function overrideSystemConfirm()
    {
        Yii::$app->view->registerJs("
yii.confirm = function(message, ok, cancel) {
  bootbox.confirm({
    title:'確認',
    message:message,
    closeButton:false,
    swapButtonOrder:true,
    buttons:{
      confirm:{
        label:'<i class=\"fa-solid fa-check\"></i> OK',
        className:'btn-primary'
      },
      cancel:{
        label:'<i class=\"fa-solid fa-xmark\"></i> キャンセル',
        className:'btn-outline-secondary'
      }
    },
    callback:function(result) {
      if (result) { !ok || ok(); } else { !cancel || cancel();}
    }
  });
};
alert = function(message) {
  bootbox.alert({
    title:'確認',
    message:message,
    closeButton:false,
    buttons:{
      ok:{
        label:'<i class=\"fa-solid fa-check\"></i> OK',
        className:'btn-primary'
      }
    }
  });
};
");
    }
}
