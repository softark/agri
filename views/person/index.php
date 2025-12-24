<?php

use app\models\Icon;
use app\models\Person;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '住所録';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">

    <h1><?= Icon::getIconAndLabel('address-book') ?></h1>

    <p>
        <?= Html::a(Icon::getIconAndLabel('address-card') . 'を新規登録', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                            'attribute' => 'name',
                            'value' => 'dispname'
                    ],
                    [
                            'attribute' => 'address',
                            'value' => 'shortaddress'
                    ],
                    'phone1',
                    'phone2',
                    'memo',
                    [
                            'class' => ActionColumn::className(),
                            'urlCreator' => function ($action, Person $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
