<?php

/* @var $this yii\web\View */

/* @var $personIdInput string */

use app\components\Icon;
use yii\bootstrap5\Modal;

?>

<?php
Modal::begin([
        'title' => '<span class="text-success">' . Icon::getIconAndLabel('map-location') . '</span>',
        'toggleButton' => false,
        'id' => 'field-map-modal',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'bodyOptions' => ['style' => 'height:85vh']
]);
?>
    <div id="field-map-container" style="width:100%; height:100%;"></div>
<?php
Modal::end();

$this->registerJs("
function openMapModal(src) {
  const modal = $('#field-map-modal');
  const container = $('#field-map-container');

  // 念のため前回の残骸を消す
  container.empty();

  // 先に modal を表示（サイズ確定）
  modal.modal('show');

  // 表示後に iframe を作る（これが効く）
  modal.one('shown.bs.modal', function () {
    const iframe = $('<iframe>', {
      id: 'map-frame',
      src: src,
      style: 'border:0; width:100%; height:100%;',
      allowfullscreen: true
    });
    container.append(iframe);
  });
}

// 閉じたら iframe を破棄（読み込み停止＋状態リセット）
$('#field-map-modal').on('hidden.bs.modal', function () {
  $('#field-map-container').empty();
});
");
