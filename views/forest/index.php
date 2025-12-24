<?php

use app\models\Forest;
use yii\helpers\Html;
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
<div class="forest-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'p_no',
                    'ko_aza',
                    'type',
                    'owner',
                //'o_addr',
                //'m_addr',
                    'manager',
                    [
                            'attribute' => 'area',
                            'value' => function ($model) {
                                return number_format($model->area, 2);
                            },
                        'contentOptions' => ['class' => 'text-end'],
                    ],
                //'area',
                //'memo',
                //'o_type',
                    [
                            'class' => ActionColumn::class,
                            'template' => '{view} {update}',
                            'urlCreator' => function ($action, Forest $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
