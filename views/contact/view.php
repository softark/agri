<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */

$this->title = $model->disp_name;
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <h1><?= Icon::getIconAndLabel('contact') . ' : ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <h2 class="h4">関係者</h2>
            <?php
            $attributes = [
                    [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return $model->typeText;
                            },
                    ],
                    'dispname',
                    'yomigana',
            ];
            if ($model->person->note != '') {
                $attributes[] = 'note';
            }
            ?>
            <?= DetailView::widget([
                    'model' => $model->person,
                    'attributes' => $attributes
            ]) ?>

            <h2 class="h4">連絡先</h2>
            <?php $attributes = [];
            if (count($model->person->contacts) > 1) $attributes[] = [
                    'attribute' => 'order',
                    'value' => function ($model) {
                        return $model->order . ' / ' . count($model->person->contacts);
                    }
            ];
            if ($model->fullname != '' && $model->fullname != $model->person->dispname) $attributes[] = 'fullname';
            if ($model->fulladdress != '') $attributes[] = 'fulladdress';
            if ($model->phones != '') $attributes[] = 'phones';
            if ($model->mail != '') $attributes[] = 'mail';
            if ($model->note != '') $attributes[] = 'note';
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
                <?php if (\yii::$app->user->can('contact.edit')) : ?>
                    <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if (\yii::$app->user->can('contact.delete')) : ?>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '連絡先 <strong>"' . $model->fullname . '"</strong> を削除しますか？（関係者は削除されません）',
                                    'method' => 'post',
                            ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
        <?php if (count($model->person->contacts) > 1): ?>
            <p>※ この関係者には複数の連絡先があります。連絡先の優先順位の変更は、関係者の閲覧画面で行うことが出来ます。
                <?= Html::a(Icon::getIcon('view') . ' ' . $model->person->dispname,
                        ['/person/view', 'id' => $model->person->id],
                        ['class' => 'btn btn-outline-primary']) ?>
            </p>
        <?php endif; ?>
    </div>
</div>
