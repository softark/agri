<?php

use app\models\FieldPerson;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Field $model */

$this->title = '農地 : ' . $model->p_no;
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->p_no;
\yii\web\YiiAsset::register($this);
?>
<div class="field-view">

    <h1><?= Icon::getIcon('field') . ' ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-4 col-md-6">
            <?php
            $attributes = [
                    'id',
                    [
                            'attribute' => 'aza_id',
                            'value' => function ($model) {
                                return $model->aza_name;
                            },
                    ],
                    'p_no',
                    [
                            'attribute' => 'f_area',
                            'value' => function ($model) {
                                return number_format($model->f_area, 2);
                            },
                    ],
            ];
            if ($model->note != '') {
                $attributes[] = 'note';
            }
            if (yii::$app->user->can('admin')) {
                $attributes[] = [
                        'label' => '登録',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm')
                                    . ' / ' . $model->createdBy->longname;
                        }
                ];
                $attributes[] = [
                        'label' => '更新',
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm')
                                    . ' / ' . $model->updatedBy->longname;
                        }
                ];
            }
            $attributes[] = [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $buttons = [];
                        if (yii::$app->user->can('field.edit')) {
                            $buttons[] = Html::a(Icon::getIconAndLabel('update'),
                                    ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]],
                                    ['class' => 'btn btn-sm btn-primary']);
                        }
                        $buttons[] = Html::a(Icon::getIcon('map-location') . ' i-GIS で見る', $model->mapurl,
                                ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank']);
                        return implode(' ', $buttons);
                    }
            ];
            ?>
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
            <h2 class="h4">所有者</h2>
            <?php if (count($model->ownerFieldPersons) == 0): ?>
                <p>
                    （未設定）
                    <?php if (\yii::$app->user->can('field.edit')) : ?>
                        <?= Html::a(Icon::getIcon('plus') . ' 所有者を登録',
                                ['add-field-person', 'id' => $model->id, 'role' => FieldPerson::ROLE_OWNER],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <?php foreach ($model->ownerFieldPersons as $ofp): ?>
                    <?php
                    $attributes = [
                            [
                                    'label' => '名前',
                                    'value' => function ($model) {
                                        return $model->person->dispname;
                                    },
                            ],
                            [
                                    'label' => '期間',
                                    'value' => function ($model) {
                                        return $model->valid_from_text . ' ～ ' . $model->valid_to_text;
                                    }
                            ],
                    ];
                    if ($ofp->note != '') {
                        $attributes[] = 'note';
                    }
                    if (\yii::$app->user->can('field.edit')) {
                        $attributes[] = [
                                'label' => '操作',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Icon::getIconAndLabel('update'),
                                            ['update-field-person', 'id' => $model->id, 'role' => FieldPerson::ROLE_OWNER],
                                            ['class' => 'btn btn-sm btn-primary']);
                                }
                        ];
                    }
                    ?>
                    <?= DetailView::widget(['model' => $ofp, 'attributes' => $attributes]) ?>
                <?php endforeach; ?>
                <?php if (\yii::$app->user->can('field.edit')) : ?>
                    <p>
                        <?= Html::a(Icon::getIcon('plus') . ' 新しい所有者を登録',
                                ['add-field-person', 'id' => $model->id, 'role' => FieldPerson::ROLE_OWNER],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
            <h2 class="h4">耕作者</h2>
            <?php if (count($model->cultivatorFieldPersons) == 0): ?>
                <p>（未設定）
                    <?php if (\yii::$app->user->can('field.edit')) : ?>
                        <?= Html::a(Icon::getIcon('plus') . ' 耕作者を登録',
                                ['add-field-person', 'id' => $model->id, 'role' => FieldPerson::ROLE_CULTIVATOR],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <?php foreach ($model->cultivatorFieldPersons as $cfp): ?>
                    <?php
                    $attributes = [
                            [
                                    'label' => '名前',
                                    'value' => function ($model) {
                                        return $model->person->dispname;
                                    },
                            ],
                            [
                                    'label' => '期間',
                                    'value' => function ($model) {
                                        return $model->valid_from_text . ' ～ ' . $model->valid_to_text;
                                    }
                            ],
                    ];
                    if ($cfp->note != '') {
                        $attributes[] = 'note';
                    }
                    if (\yii::$app->user->can('field.edit')) {
                        $attributes[] = [
                                'label' => '操作',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Icon::getIconAndLabel('update'),
                                            ['update-field-person', 'id' => $model->id],
                                            ['class' => 'btn btn-sm btn-primary']);
                                }
                        ];
                    }
                    ?>
                    <?= DetailView::widget(['model' => $cfp, 'attributes' => $attributes]) ?>
                <?php endforeach; ?>
                <?php if (\yii::$app->user->can('field.edit')) : ?>
                    <p>
                        <?= Html::a(Icon::getIcon('plus') . ' 新しい管理者を登録',
                                ['add-field-person', 'id' => $model->id, 'role' => FieldPerson::ROLE_CULTIVATOR],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
            <h2 class="h4">農地利用状況</h2>
            <?php if (count($model->fieldUsages) == 0): ?>
                <p>（未設定）
                    <?php if (\yii::$app->user->can('field.edit')) : ?>
                        <?= Html::a(Icon::getIcon('plus') . ' 農地利用状況を登録',
                                ['add-field-usage', 'id' => $model->id],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <?php foreach ($model->fieldUsages as $fu): ?>
                    <?php
                    $attributes = [
                            [
                                    'label' => '農地利用状況',
                                    'value' => function ($model) {
                                        return $model->usage->name;
                                    },
                            ],
                            [
                                    'label' => '期間',
                                    'value' => function ($model) {
                                        return $model->valid_from_text . ' ～ ' . $model->valid_to_text;
                                    }
                            ],
                    ];
                    if ($fu->note != '') {
                        $attributes[] = 'note';
                    }
                    if (\yii::$app->user->can('field.edit')) {
                        $attributes[] = [
                                'label' => '操作',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Icon::getIconAndLabel('update'),
                                            ['update-field-usage', 'id' => $model->id],
                                            ['class' => 'btn btn-sm btn-primary']);
                                }
                        ];
                    }
                    ?>
                    <?= DetailView::widget(['model' => $fu, 'attributes' => $attributes]) ?>
                <?php endforeach; ?>
                <?php if (\yii::$app->user->can('field.edit')) : ?>
                    <p>
                        <?= Html::a(Icon::getIcon('plus') . ' 新しい利用状況を登録',
                                ['add-field-usage', 'id' => $model->id],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
        </div>
    </div>
</div>