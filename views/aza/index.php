<?php

use app\components\Icon;
use app\models\Aza;
use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\AzaSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '字（あざ）';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aza-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Icon::getIcon('plus') . ' 字（あざ）を新規登録', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Aza $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
