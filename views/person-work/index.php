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

$this->title = '名簿ワーク';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div id="person-work-index" class="person-work-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('棚田テーブルからインポート', ['import-tanada'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '棚田テーブルから名簿ワークのエントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('山林テーブルからインポート', ['import-forest'], [
                    'class' => 'btn btn-success',
                    'data' => [
                            'confirm' => '山林テーブルから名簿ワークのエントリをインポートしますか？',
                            'method' => 'post',
                    ],
            ]) ?>
            <?= Html::a('初期化', ['init'], [
                    'class' => 'btn btn-danger',
                    'data' => [
                            'confirm' => '名簿ワークを初期化しますか？',
                            'method' => 'post',
                    ],
            ]) ?>
        </p>

        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
                'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                                'attribute' => 'src',
                                'value' => 'srcText',
                        ],
                        'name',
                        'address',
                        [
                                'attribute' => 'person_id',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-card-button'],
                                'value' => function ($model) {
                                    return $model->person_id !== null ?
                                            Html::a($model->person->dispname, ['/person/view', 'id' => $model->person_id],
                                                    ['class' => 'btn btn-primary btn-sm']) :
                                            '';
                                }
                        ],
                        [
                                'attribute' => 'contact_id',
                                'format' => 'raw',
                                'contentOptions' => ['class' => 'col-card-button'],
                                'value' => function ($model) {
                                    return $model->contact_id !== null ?
                                            Html::a($model->contact->address, ['/contact/view', 'id' => $model->contact_id],
                                                    ['class' => 'btn btn-primary btn-sm']) :
                                            '';
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

    </div>
