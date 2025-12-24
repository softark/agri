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
        <?= Html::a('棚田テーブルからインポート', ['import-tanada'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('山林テーブルからインポート', ['import-forest'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('初期化', ['init'], ['class' => 'btn btn-danger']) ?>
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
            'person_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, PersonWork $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
