<?php

use app\models\Icon;
use app\models\PersonWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonWorkSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '名簿ワーク';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div id="person-work-index" class="person-work-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('棚田テーブルからインポート', ['import-tanada'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '棚田テーブルから名簿ワークのエントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('山林テーブルからインポート', ['import-forest'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '山林テーブルから名簿ワークのエントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('初期化', ['init'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => '名簿ワークを初期化しますか？',
                            'method' => 'post',
                    ],
            ]) ?>
        </p>

        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'name',
                        'address',
                        [
                                'attribute' => 'person_id',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-card-button'],
                                'value' => function ($model) {
                                    return $model->person_id !== null ?
                                            Html::a($model->person->dispname, ['/person/view', 'id' => $model->person_id],
                                                    ['class' => 'btn btn-primary btn-sm']) :
                                            Html::a('住所カード登録', ['/person/create', 'work_id' => $model->id],
                                                    ['class' => 'btn btn-success btn-sm']);
                                }
                        ],
                        [
                                'label' => '住所カードリンク',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-link-buttons'],
                                'value' => function ($model) {
                                    if ($model->person_id !== null) {
                                        return Html::button(
                                                        Icon::getIcon('link') . ' リンク変更',
                                                        ['class' => 'btn btn-primary btn-sm add-link', 'data-model-id' => $model->id]
                                                )
                                                . ' ' .
                                                Html::button(
                                                        Icon::getIconAndLabel('unlink'),
                                                        ['class' => 'btn btn-sm btn-danger del-link', 'data-model-id' => $model->id]
                                                );
                                    } else {
                                        return Html::button(
                                                Icon::getIconAndLabel('link'),
                                                ['class' => 'btn btn-success btn-sm add-link', 'data-model-id' => $model->id]
                                        );
                                    }
                                }
                        ],
                        [
                                'class' => ActionColumn::className(),
                                'template' => '{view} {delete}',
                                'urlCreator' => function ($action, PersonWork $model, $key, $index, $column) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                }
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>

        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('model_id', '', ['id' => 'model-id']) ?>
        <?php
        echo $this->render('/person/_select_modal.php', [
                'personIdInput' => 'person-id',
        ]);
        ?>
    </div>

<?php
$urlAdd = Url::to(['/person-work/add-link']);
$urlDel = Url::to(['/person-work/delete-link']);

$this->registerJs("
$('#person-work-index').on('click', '.add-link', function(event){
    event.preventDefault();
    $('#model-id').val($(this).data('model-id'));
    openPersonSelectModal();
});

$('#person-id').on('change', function () {
  const modelId = $('#model-id').val();
  const personId = $(this).val();

  $.ajax({
    url: '$urlAdd',
    type: 'POST',
    dataType: 'json',
    data: { id: modelId, person_id: personId, _csrf: yii.getCsrfToken() },
    success: function (res) {
      if (!res.ok) { alert(res.message ?? '更新に失敗'); return; }

      // ボタン（=クリック元）から行を取るのが確実
      const row = $('.add-link[data-model-id=' + res.id + ']').closest('tr');

      row.find('.col-card-button').html(res.cardHtml);
      row.find('.col-link-buttons').html(res.buttonsHtml);
    },
    error: function (xhr) {
      alert('更新に失敗しました: ' + xhr.status);
    }
  });
});

$('#person-work-index').on('click', '.del-link', function (event) {
  event.preventDefault();
  event.stopPropagation();
  const modelId = $(this).data('model-id');
  const row = $(this).closest('tr');
  yii.confirm('住所カードへのリンクを削除しますか？', function () {
    $.ajax({
      url: '$urlDel',
      type: 'POST',
      dataType: 'json',
      data: { id: modelId, _csrf: yii.getCsrfToken() },
      success: function (res) {
        if (!res.ok) { alert(res.message ?? '削除に失敗'); return; }
        row.find('.col-card-button').html(res.cardHtml);
        row.find('.col-link-buttons').html(res.buttonsHtml);
      },
      error: function (xhr) {
        alert('削除に失敗しました: ' + xhr.status);
      }
    });
  });
});
");
