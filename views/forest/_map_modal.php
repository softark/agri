<?php

/* @var $this yii\web\View */

/* @var $personIdInput string */

use yii\bootstrap5\Modal;

?>

<?php
Modal::begin([
        'title' => '山林地図',
        'toggleButton' => false,
        'id' => 'forest-map-modal',
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
    $('#forest-map-modal').modal('show');
}

$('#forest-map-modal').on('hidden.bs.modal', () => {
  // 閉じたら読み込み停止（重要）
  $('#map-frame').attr('src', '');
});
");
