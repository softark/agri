<?php

use app\models\Forest;
use app\models\ForestSearch;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\ForestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '山林';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forest-index" id="forest-index">

    <h1><?= Icon::getIconAndLabel('tree') ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'showFooter' => true,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                            'attribute' => 'aza_id',
                            'value' => function ($model) {
                                return $model->aza_name;
                            }
                    ],
                    'p_no',
                    [
                            'attribute' => 'type_id',
                            'value' => function ($model) {
                                return $model->type_name;
                            }
                    ],
                    [
                            'attribute' => 'owner_id',
                            'value' => function ($model) {
                                return $model->owner_name;
                            }
                    ],
                    [
                            'attribute' => 'manager_id',
                            'value' => function ($model) {
                                return $model->manager_name;
                            }
                    ],
                    [
                            'attribute' => 'area',
                            'value' => function ($model) {
                                return number_format($model->area, 2);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                            'footer' => number_format(ForestSearch::getAreaTotal($dataProvider), 2),
                            'footerOptions' => ['class' => 'text-end'],
                    ],
                    'note',
                    [
                            'class' => ActionColumn::className(),
                            'template' => '{view} {update}',
                            'urlCreator' => function ($action, Forest $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
                    [
                            'label' => '地図',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::button(Icon::getIconAndLabel('map-location'),
                                        ['class' => 'btn-map-open btn btn-sm btn-primary', 'data-url' => $model->mapurl])
                                        . ' ' .
                                        Html::a(Icon::getIcon('map-location') . ' i-GIS で見る',
                                                $model->mapurl,['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank']);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>
    <?= $this->render('/forest/_map_modal') ?>

</div>

<?php
$this->registerJs("
$('#forest-index').on('click', '.btn-map-open', function(e) {
  e.preventDefault();
  const src = $(this).data('url');
  openMapModal(src);
});
");
