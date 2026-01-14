<?php

use app\models\Frtype;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\FrtypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '山林タイプ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frtype-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Icon::getIcon('plus') . ' 山林タイプを新規登録', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'order',
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Frtype $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
