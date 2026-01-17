<?php

use app\models\ForestPerson;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */

$this->title = '山林 : ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;
\yii\web\YiiAsset::register($this);
?>
<div class="forest-view">

    <h1><?= Icon::getIcon('tree') . ' ' . Html::encode($this->title) ?></h1>

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
                            'attribute' => 'type_id',
                            'value' => function ($model) {
                                return $model->type_name;
                            },
                    ],
                    [
                            'attribute' => 'area',
                            'value' => function ($model) {
                                return number_format($model->area, 2);
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
                        if (yii::$app->user->can('forest.edit')) {
                            $buttons[] = Html::a(Icon::getIconAndLabel('update'),
                                    ['update', 'id' => $model->id],
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
            <?php if (count($model->ownerForestPersons) == 0): ?>
                <p>
                    （未設定）
                    <?php if (\yii::$app->user->can('forest.edit')) : ?>
                        <?= Html::a(Icon::getIcon('plus') . ' 所有者を登録',
                                ['add-fp', 'id' => $model->id, 'role' => ForestPerson::ROLE_OWNER],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <?php foreach ($model->ownerForestPersons as $ofp): ?>
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
                    if (\yii::$app->user->can('forest.edit')) {
                        $attributes[] = [
                                'label' => '操作',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::a(Icon::getIconAndLabel('update'),
                                            ['update-forest-person', 'id' => $model->id, 'role' => ForestPerson::ROLE_OWNER],
                                            ['class' => 'btn btn-sm btn-primary']);
                                }
                        ];
                    }
                    ?>
                    <?= DetailView::widget(['model' => $ofp, 'attributes' => $attributes]) ?>
                <?php endforeach; ?>
                <?php if (\yii::$app->user->can('forest.edit')) : ?>
                    <p>
                        <?= Html::a(Icon::getIcon('plus') . ' 新しい所有者を登録',
                                ['add-forest-person', 'id' => $model->id, 'role' => ForestPerson::ROLE_OWNER],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
            <h2 class="h4">管理者</h2>
            <?php if (count($model->managerForestPersons) == 0): ?>
                <p>（未設定）
                    <?php if (\yii::$app->user->can('forest.edit')) : ?>
                        <?= Html::a(Icon::getIcon('plus') . ' 管理者を登録',
                                ['add-forest-person', 'id' => $model->id, 'role' => ForestPerson::ROLE_MANAGER],
                                ['class' => 'btn btn-sm btn-primary']) ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
            <?php foreach ($model->managerForestPersons as $mfp): ?>
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
                if ($mfp->note != '') {
                    $attributes[] = 'note';
                }
                if (\yii::$app->user->can('forest.edit')) {
                    $attributes[] = [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a(Icon::getIconAndLabel('update'),
                                        ['update-forest-person', 'id' => $model->id],
                                        ['class' => 'btn btn-sm btn-primary']);
                            }
                    ];
                }
                ?>
                <?= DetailView::widget(['model' => $mfp, 'attributes' => $attributes]) ?>
            <?php endforeach; ?>
            <?php if (\yii::$app->user->can('forest.edit')) : ?>
                <p>
                    <?= Html::a(Icon::getIcon('plus') . ' 新しい管理者を登録',
                            ['add-forest-person', 'id' => $model->id, 'role' => ForestPerson::ROLE_MANAGER],
                            ['class' => 'btn btn-sm btn-primary']) ?>
                </p>
            <?php endif; ?>
        </div>
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
        </div>
    </div>
</div>
