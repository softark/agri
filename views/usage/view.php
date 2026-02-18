<?php

use app\components\Icon;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Usage $model */

$this->title = '農地利用状況 : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '農地利用状況', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="usage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('usage.edit')) : ?>
            <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
            <?php if (Yii::$app->user->can('usage.delete')) : ?>
                <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                                'confirm' => 'この農地利用状況を削除しても構いませんか？?',
                                'method' => 'post',
                        ],
                ]) ?>
            <?php endif; ?>
        <?php endif; ?>
        <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            [
                                    'attribute' => 'type',
                                    'value' => function ($model) {
                                        return $model->typeText;
                                    }
                            ],
                            'order',
                            'name',
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
                    ],
            ]) ?>
        </div>
    </div>

</div>
