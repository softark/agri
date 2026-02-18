<?php

use app\components\Icon;
use app\models\Usage;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\UsageSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '農地利用状況';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usage-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('usage.create')) : ?>
        <p>
            <?= Html::a(Icon::getIcon('plus') . ' 農地利用状況を新規登録', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'order',
                    [
                            'attribute' => 'type',
                            'value' => 'typeText',
                    ],
                    'name',
                    [
                            'class' => ActionColumn::className(),
                            'template' => Yii::$app->user->can('usage.delete') ? '{view} {update} {delete}' :
                                    (Yii::$app->user->can('usage.edit') ? '{view} {update}' : '{view}'),
                            'urlCreator' => function ($action, Usage $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
