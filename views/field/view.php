<?php

use app\components\Icon;
use app\models\Field;
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
            <p>
                <?php if (yii::$app->user->can('forest.edit')): ?>
                    <?= Html::a(Icon::getIconAndLabel('update'),
                            ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]],
                            ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIcon('map-location') . ' i-GIS', $model->mapurl,
                        ['class' => 'btn btn-outline-success', 'target' => '_blank']) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
            <?php
            $attributes = [
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
                                return Field::getAreaTextFull($model->f_area);
                            },
                    ],
                    [
                            'attribute' => 'c_area',
                            'value' => function ($model) {
                                return Field::getAreaTextFull($model->c_area);
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
            ?>
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => $attributes,
            ]) ?>
            <h2 class="h5">所有者</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>所有者</th>
                    <th>メモ</th>
                    <?php if (Yii::$app->user->can('field.edit')): ?>
                        <th>削除</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->ownerFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                        <?php if (Yii::$app->user->can('field.edit')): ?>
                            <td>&nbsp;</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->ownerFieldPersons as $ofp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $ofp->valid_from_text ?></td>
                            <td><?= Html::a($ofp->person->dispname, ['/person/view', 'id' => $ofp->person_id],
                                        ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $ofp->note ?></td>
                            <?php if (Yii::$app->user->can('field.edit')): ?>
                                <td>
                                    <?=
                                    Html::a(Icon::getIcon('delete'), ['delete-field-person', 'id' => $ofp->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                            'confirm' => "この所有者 [{$ofp->person->dispname}] を削除しますか？",
                                                            'method' => 'post',
                                                    ]
                                            ]);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <h2 class="h5">耕作者</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>耕作者</th>
                    <th>メモ</th>
                    <?php if (Yii::$app->user->can('field.edit')): ?>
                        <th>削除</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->cultivatorFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                        <?php if (Yii::$app->user->can('field.edit')): ?>
                            <td>&nbsp;</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->cultivatorFieldPersons as $cfp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $cfp->valid_from_text ?></td>
                            <td><?= Html::a($cfp->person->dispname, ['/person/view', 'id' => $cfp->person_id],
                                        ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $cfp->note ?></td>
                            <?php if (Yii::$app->user->can('field.edit')): ?>
                                <td>
                                    <?=
                                    Html::a(Icon::getIcon('delete'), ['delete-field-person', 'id' => $cfp->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                            'confirm' => "この耕作者 [{$cfp->person->dispname}] を削除しますか？",
                                                            'method' => 'post',
                                                    ]
                                            ]);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <h2 class="h5">中山間名義人</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>中山間名義人</th>
                    <th>メモ</th>
                    <?php if (Yii::$app->user->can('field.edit')): ?>
                        <th>削除</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->chusankanFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                        <?php if (Yii::$app->user->can('field.edit')): ?>
                            <td>&nbsp;</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->chusankanFieldPersons as $chfp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $chfp->valid_from_text ?></td>
                            <td><?= Html::a($chfp->person->dispname, ['/person/view', 'id' => $chfp->person_id],
                                        ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $chfp->note ?></td>
                            <?php if (Yii::$app->user->can('field.edit')): ?>
                                <td>
                                    <?=
                                    Html::a(Icon::getIcon('delete'), ['delete-field-person', 'id' => $chfp->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                            'confirm' => "この中山間名義人 [{$chfp->person->dispname}] を削除しますか？",
                                                            'method' => 'post',
                                                    ]
                                            ]);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <h2 class="h5">細目書名義人</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>細目書名義人</th>
                    <th>メモ</th>
                    <?php if (Yii::$app->user->can('field.edit')): ?>
                        <th>削除</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->saimokushoFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                        <?php if (Yii::$app->user->can('field.edit')): ?>
                            <td>&nbsp;</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->saimokushoFieldPersons as $safp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $safp->valid_from_text ?></td>
                            <td><?= Html::a($safp->person->dispname, ['/person/view', 'id' => $safp->person_id],
                                        ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $safp->note ?></td>
                            <?php if (Yii::$app->user->can('field.edit')): ?>
                                <td>
                                    <?=
                                    Html::a(Icon::getIcon('delete'), ['delete-field-person', 'id' => $safp->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                            'confirm' => "この細目書名義人 [{$safp->person->dispname}] を削除しますか？",
                                                            'method' => 'post',
                                                    ]
                                            ]);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <h2 class="h5">利用状況</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>利用状況</th>
                    <th>メモ</th>
                    <?php if (Yii::$app->user->can('field.edit')): ?>
                        <th>削除</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->fieldUsages) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                        <?php if (Yii::$app->user->can('field.edit')): ?>
                            <td>&nbsp;</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->fieldUsages as $fu): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $fu->valid_from_text ?></td>
                            <td><?= $fu->usage->name ?></td>
                            <td><?= $fu->note ?></td>
                            <?php if (Yii::$app->user->can('field.edit')): ?>
                                <td>
                                    <?=
                                    Html::a(Icon::getIcon('delete'), ['delete-field-usage', 'id' => $fu->id],
                                            [
                                                    'class' => 'btn btn-danger btn-sm',
                                                    'data' => [
                                                            'confirm' => "この利用状況 [{$fu->usage->name}] を削除しますか？",
                                                            'method' => 'post',
                                                    ]
                                            ]);
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $model->mapurl ?>" style="width:100%; height:550px;"></iframe>
        </div>
    </div>
</div>

