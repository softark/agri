<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '住所録辞書', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

    <div id="person-work-view" class="person-work-view">

        <h1><?= Html::encode($this->title) ?></h1>

        <div class="row">
            <div class="col-lg-6 col-md-8">

                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                                'id',
                                'name',
                                'address',
                        ],
                ]) ?>
                <p>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '住所録辞書エントリ <strong>"' . $model->name . '"</strong> を削除しますか？',
                                    'method' => 'post',
                            ],
                    ]) ?>
                    <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                </p>

                <h3>住所カードへのリンク</h3>
                <div id="card-link">
                    <?= $this->render('_card_link', ['model' => $model]) ?>
                </div>
                <?php
                echo $this->render('/person/_select_modal.php', [
                        'personIdInput' => 'person-id',
                ]);
                ?>

            </div>
        </div>
    </div>

<?php
$urlAdd = Url::to(['person-work/add-link-view', 'id' => $model->id]);
$urlDel = Url::to(['person-work/delete-link-view', 'id' => $model->id]);
$this->registerJs("
$('#card-link').on('click', '#btn-person-select', function(event){
    openPersonSelectModal();
    event.preventDefault();
});
$('#card-link').on('change', '#person-id', function() {
  const personId = $(this).val();
  $.ajax({
    url: '$urlAdd',
    type: 'POST',
    data: {
      person_id: personId,
      _csrf: yii.getCsrfToken()
    },
    success: function (html) {
      $('#card-link').html(html);
    },
    error: function (xhr) {
      alert('更新に失敗しました: ' + xhr.status);
    }
  });
});

$('#card-link').on('click', '#btn-person-unlink', function(event){
  event.preventDefault();
  event.stopPropagation();
  yii.confirm('住所カードへのリンクを削除しますか？', function () {
    $.ajax({
      url: '$urlDel',
      type: 'POST',
      data: { _csrf: yii.getCsrfToken() },
      success: function (html) {
        $('#card-link').html(html);
      },
      error: function (xhr) {
        alert('削除に失敗しました: ' + xhr.status);
      }
    });
  });
});
");
