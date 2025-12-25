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

$this->title = '住所録辞書';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="person-work-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('棚田テーブルからインポート', ['import-tanada'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '棚田テーブルから住所録辞書エントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('山林テーブルからインポート', ['import-forest'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '山林テーブルから住所録辞書エントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('初期化', ['init'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => '住所録辞書を初期化削除しますか？',
                            'method' => 'post',
                    ],
            ]) ?>
        </p>

        <?php Pjax::begin(); ?>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
                'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'name',
                        'address',
                        [
                                'attribute' => 'person_id',
                                'value' => function ($model) {
                                    if ($model->person_id != null) {
                                        return $model->person->dispname;
                                    } else {
                                        return '（リンクなし）';
                                    }
                                }
                        ],
                        [
                                'label' => 'リンク',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    if ($model->person_id != null) {
                                        return Html::button(Icon::getIcon('link') . ' リンク変更', ['class' => 'btn btn-primary btn-sm add-link', 'data' => ['model-id' => $model->id]])
                                                . ' '
                                                . Html::a(Icon::getIconAndLabel('unlink'), ['delete-link', 'id' => $model->id], [
                                                        'class' => 'btn btn-sm btn-danger',
                                                        'data' => [
                                                                'confirm' => '住所カードへのリンクを削除しますか？',
                                                                'method' => 'post',
                                                        ],
                                                ]);

                                    } else {
                                        return Html::button(Icon::getIconAndLabel('link'), ['class' => 'btn btn-success btn-sm add-link', 'data' => ['model-id' => $model->id]]);
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
$url = Url::to(['/person-work/add-link']);
$this->registerJs("
$('.add-link').on('click', function(event){
    event.preventDefault();
    $('#model-id').val($(this).data('model-id'));
    openPersonSelectModal();
});
$('#person-id').on('change', function() {
  $.post('$url' + '?id=' + $('#model-id').val() + '&person_id=' + $(this).val());
});
");
