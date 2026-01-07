<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */

$this->title = '名簿 : ' . $model->person->dispname . ' / 連絡先 : ' . $model->fullname;
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <h1><?= Icon::getIcon('contact') . ' ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <?= DetailView::widget([
                    'model' => $model->person,
                    'attributes' => [
                            [
                                    'attribute' => 'type',
                                    'value' => function ($model) {
                                        return $model->typeText;
                                    },
                            ],
                            'dispname',
                            'yomigana',
                            'note',
                    ],
            ]) ?>

            <?php $attributes = [
                    'order',
                    'role',
                    'dispname',
                    'fulladdress',
                    'phones',
                    'mail',
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
            <p>
                <?php if (\yii::$app->user->can('contact.edit', ['id' => $model->id])) : ?>
                    <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if (\yii::$app->user->can('contact.delete')) : ?>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '連絡先 <strong>"' . $model->fullname . '"</strong> を削除しますか？（名簿は削除されません）',
                                    'method' => 'post',
                            ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>
</div>
