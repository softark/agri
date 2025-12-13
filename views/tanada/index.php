<?php

use app\models\Tanada;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\TanadaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '棚田';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tanada-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'p_no',
                    'owner',
                    [
                            'attribute' => 'area',
                            'value' => function ($model) {
                                return sprintf('%.02f', $model->area);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                    ],
                    'cultivator',
                    'usage',
                    [
                            'class' => ActionColumn::className(),
                            'template' => '{view} {update}',
                            'urlCreator' => function ($action, Tanada $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
