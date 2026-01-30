<?php

use app\models\Icon;
use app\models\PersonRelation;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonRelationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '引継';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-relation-index">

    <h1><?= Icon::getIconAndLabel('succeed') ?></h1>

    <p>
        <?= Html::a(Icon::getIcon('plus') . ' 引継の登録', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000]) ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <div class="col-lg-8">
            <?php
            $template = '{view}';
            if (Yii::$app->user->can('/person-relation/update')) {
                $template .= ' {update}';
            }
            if (Yii::$app->user->can('/person-relation/delete')) {
                $template .= ' {delete}';
            }
            ?>

            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                // 'filterModel' => $searchModel,
                    'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                    'attribute' => 'from_person_id',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a($model->fromPerson->dispname,
                                                ['/person/view', 'id' => $model->from_person_id],
                                                ['class' => 'btn btn-sm btn-outline-primary']);
                                    }
                            ],
                            [
                                    'attribute' => 'to_person_id',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        return Html::a($model->toPerson->dispname,
                                                ['/person/view', 'id' => $model->to_person_id],
                                                ['class' => 'btn btn-sm btn-outline-primary']);
                                    }
                            ],
                            'note',
                            [
                                    'class' => ActionColumn::class,
                                    'template' => $template,
                                    'urlCreator' => function ($action, PersonRelation $model, $key, $index, $column) {
                                        return Url::toRoute([$action, 'id' => $model->id]);
                                    }
                            ],
                    ],
            ]); ?>
        </div>
    </div>
    <?php Pjax::end(); ?>

</div>
