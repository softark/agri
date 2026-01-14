<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonRelation $model */

$this->title = '引継 : ' . $model->fromPerson->dispname . ' > ' . $model->toPerson->dispname;
$this->params['breadcrumbs'][] = ['label' => '引継', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="person-relation-view">

    <h1><?= Icon::getIcon('succeed') . ' ' . Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                        'confirm' => 'このデータを削除しても構いませんか？',
                        'method' => 'post',
                ],
        ]) ?>
    </p>
    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?php
            $attributes = [
                    [
                            'attribute' => 'from_person_id',
                            'value' => function ($model) {
                                return $model->fromPerson->dispname;
                            },
                    ],
                    [
                            'attribute' => 'to_person_id',
                            'value' => function ($model) {
                                return $model->toPerson->dispname;
                            },
                    ],
                    'note',
            ];
            if (Yii::$app->user->can('admin')) {
                $attributes = ArrayHelper::merge($attributes, [
                        [
                                'label' => '登録',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm')
                                            . ' / ' . $model->createdBy->longname;
                                }
                        ],
                        [
                                'label' => '更新',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm')
                                            . ' / ' . $model->updatedBy->longname;
                                }
                        ],
                ]);
            }
            ?>
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
            ]) ?>
        </div>
    </div>
    <p>
        <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

</div>
