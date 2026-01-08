<?php

use app\models\Icon;
use app\models\PersonWork;
use yii\bootstrap5\Html;
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
            <?= Html::a('山林テーブルからインポート', ['import-isg-forest'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '山林テーブルから名簿ワークのエントリをインポートしますか？',
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
                        [
                                'attribute' => 'src',
                                'value' => 'srcText',
                        ],
                        'name',
                        'address',
                        [
                                'label' => '登録',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-person-register'],
                                'value' => function ($model) {
                                    return $model->person_id === null ?
                                            Html::a('登録', ['register', 'id' => $model->id],
                                                    ['class' => 'btn btn-success btn-sm']) :
                                            '';
                                }
                        ],
                        [
                                'label' => '名簿へのリンク',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-link-buttons'],
                                'value' => function ($model) {
                                    if ($model->person_id !== null) {
                                        return Html::button(
                                                        Icon::getIcon('link') . ' 変更',
                                                        ['class' => 'btn btn-primary btn-sm add-link', 'data-model-id' => $model->id]
                                                )
                                                . ' ' .
                                                Html::button(
                                                        Icon::getIcon('unlink') . ' 解除',
                                                        ['class' => 'btn btn-sm btn-danger del-link', 'data-model-id' => $model->id]
                                                );
                                    } else {
                                        return Html::button(
                                                Icon::getIcon('link') . ' 選択',
                                                ['class' => 'btn btn-success btn-sm add-link', 'data-model-id' => $model->id]
                                        );
                                    }
                                }
                        ],
                        [
                                'attribute' => 'person_id',
                                'label' => '名簿・連絡先',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-link-person'],
                                'value' => function ($model) {
                                    if ($model->person_id !== null) {
                                        $label = $model->person->fullname;
                                        return Html::a($label, ['/person/view', 'id' => $model->person_id], []);
                                    } else {
                                        return '&nbsp;';
                                    }
                                }
                        ],
                        [
                                'class' => ActionColumn::className(),
                                'template' => '{view}',
                                'urlCreator' => function ($action, PersonWork $model, $key, $index, $column) {
                                    return Url::toRoute([$action, 'id' => $model->id]);
                                }
                        ],
                ],
        ]); ?>

        <?php Pjax::end(); ?>

        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
        <?= Html::hiddenInput('model_id', '', ['id' => 'model-id']) ?>
        <?php
        echo $this->render('/person/_select_modal.php', [
                'personIdInput' => 'person-id',
                'personNameInput' => 'person-name',
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
      if (!res.ok) { alert(res.message ?? 'リンクの更新に失敗しました'); return; }
      // ボタン（=クリック元）から行を取るのが確実
      const row = $('.add-link[data-model-id=' + res.id + ']').closest('tr');
      row.find('.col-person-register').html(res.personRegisterHtml);
      row.find('.col-link-buttons').html(res.linkButtonsHtml);
      row.find('.col-link-person').html(res.linkPersonHtml);
      row.find('.col-link-contact').html(res.linkContactHtml);
    },
    error: function (xhr) {
      alert('リンクの更新に失敗しました: ' + xhr.status);
    }
  });
});

$('#person-work-index').on('click', '.del-link', function (event) {
  event.preventDefault();
  event.stopPropagation();
  const modelId = $(this).data('model-id');
  const row = $(this).closest('tr');
  yii.confirm('名簿へのリンクを削除しますか？', function () {
    $.ajax({
      url: '$urlDel',
      type: 'POST',
      dataType: 'json',
      data: { id: modelId, _csrf: yii.getCsrfToken() },
      success: function (res) {
        if (!res.ok) { alert(res.message ?? 'リンクの削除に失敗しました。'); return; }
        row.find('.col-person-register').html(res.personRegisterHtml);
        row.find('.col-link-buttons').html(res.linkButtonsHtml);
        row.find('.col-link-person').html(res.linkPersonHtml);
        row.find('.col-link-contact').html(res.linkContactHtml);
      },
      error: function (xhr) {
        alert('リンクの削除に失敗しました: ' + xhr.status);
      }
    });
  });
});
");
