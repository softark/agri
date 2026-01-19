<?php

use app\models\Field;
use app\models\FieldSearch;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\FieldSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '農地';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index" id="field-index">

    <h1><?= Icon::getIconAndLabel('field') ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'showFooter' => true,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'aza_id',
                    'p_no',
                    [
                            'attribute' => 'owner',
                            'value' => function ($model) {
                                return $model->owner_name;
                            }
                    ],
                    [
                            'attribute' => 'cultivator',
                            'value' => function ($model) {
                                return $model->cultivator_name;
                            }
                    ],
                    [
                            'attribute' => 'usage',
                            'value' => function ($model) {
                                return $model->usage_name;
                            }
                    ],
                    [
                            'attribute' => 'f_area',
                            'value' => function ($model) {
                                return number_format($model->c_area, 2);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                            'footer' => number_format(FieldSearch::getFAreaTotal($dataProvider), 2),
                            'footerOptions' => ['class' => 'text-end'],
                    ],
                    'note',
                    [
                            'class' => ActionColumn::className(),
                            'template' => '{view} {update}',
                            'urlCreator' => function ($action, Field $model, $key, $index, $column) {
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
    <?= $this->render('/field/_map_modal') ?>

</div>
<?php

$this->registerJs("
$('#field-index').on('click', '.btn-map-open', function(e) {
  e.preventDefault();
  const src = $(this).data('url');
  openMapModal(src);
});
");
