<?php

/* @var $this yii\web\View */

/* @var $personIdInput string */

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;

?>

<?php
Modal::begin([
        'title' => '<span class="text-success">' . Icon::getIconAndLabel('map-location') . '</span>',
        'toggleButton' => false,
        'id' => 'igis-map-modal',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'bodyOptions' => ['style' => 'height:85vh']
]);
?>
    <iframe id="map-frame" src="" style="border:0; width:100%; height:100%;" allowfullscreen></iframe>

<?php
Modal::end();

$this->registerJs("
function openMapModal(src) {
    $('#map-frame').attr('src', src);
    $('#igis-map-modal').modal('show');
}

$('#igis-map-modal').on('hidden.bs.modal', () => {
  // 閉じたら読み込み停止（重要）
  $('#map-frame').attr('src', '');
});
");
