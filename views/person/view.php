<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = $model->dispname;
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <h1><?= Icon::getIconAndLabel('person') . ' : ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">
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
                    'note',
            ];
            if (Yii::$app->user->can('admin')) {
                $attributes = ArrayHelper::merge($attributes, [
                        [
                                'attribute' => 'created_at',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm:ss');
                                }
                        ],
                        [
                                'attribute' => 'created_by',
                                'value' => function ($model) {
                                    return $model->createdBy->longname;
                                }
                        ],
                        [
                                'attribute' => 'updated_at',
                                'value' => function ($model) {
                                    return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm:ss');
                                }
                        ],
                        [
                                'attribute' => 'updated_by',
                                'value' => function ($model) {
                                    return $model->updatedBy->longname;
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
                <?php if (\yii::$app->user->can('person.edit', ['id' => $model->id])) : ?>
                    <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if (\yii::$app->user->can('person.delete')) : ?>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '名簿から <strong>"' . $model->dispname . '"</strong> を削除しますか？',
                                    'method' => 'post',
                            ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
            <hr />
            <h3>連絡先</h3>
            <?php if (count($model->contacts) == 0) : ?>
                （登録なし）
            <?php else: ?>
                <?php foreach ($model->contacts as $contact) : ?>
                    <?php
                    $attributes = [
                            'order',
                            'role',
                            'contact_name',
                            'zip',
                            'address1',
                            'address2',
                            'phone1',
                            'phone2',
                            'mail',
                            'note',
                    ];
                    if (Yii::$app->user->can('admin')) {
                        $attributes = ArrayHelper::merge($attributes, [
                                [
                                        'attribute' => 'created_at',
                                        'value' => function ($model) {
                                            return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm:ss');
                                        }
                                ],
                                [
                                        'attribute' => 'created_by',
                                        'value' => function ($model) {
                                            return $model->createdBy->longname;
                                        }
                                ],
                                [
                                        'attribute' => 'updated_at',
                                        'value' => function ($model) {
                                            return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm:ss');
                                        }
                                ],
                                [
                                        'attribute' => 'updated_by',
                                        'value' => function ($model) {
                                            return $model->updatedBy->longname;
                                        }
                                ],
                        ]);
                    }
                    ?>
                    <?= DetailView::widget([
                            'model' => $contact,
                            'attributes' => $attributes,
                    ]) ?>
                    <p>
                        <?php if (\yii::$app->user->can('contact.edit', ['id' => $contact->id])) : ?>
                            <?= Html::a(Icon::getIconAndLabel('update'), ['/contact/update', 'id' => $contact->id], ['class' => 'btn btn-primary']) ?>
                        <?php endif; ?>
                        <?php if (\yii::$app->user->can('contact.delete')) : ?>
                            <?= Html::a(Icon::getIconAndLabel('delete'), ['/contact/delete', 'id' => $contact->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                            'confirm' => '名簿からこの連絡先を削除しますか？',
                                            'method' => 'post',
                                    ],
                            ]) ?>
                        <?php endif; ?>
                    </p>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
