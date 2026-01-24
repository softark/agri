<?php

use app\models\Field;
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
            <p>
                <?php if (yii::$app->user->can('forest.edit')): ?>
                    <?= Html::a(Icon::getIconAndLabel('update'),
                            ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]],
                            ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIcon('map-location') . ' i-GIS で見る', $model->mapurl,
                        ['class' => 'btn btn-outline-primary', 'target' => '_blank']) ?>
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
                    <th>TO</th>
                    <th>所有者</th>
                    <th>メモ</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->ownerFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>現在</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->ownerFieldPersons as $ofp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $ofp->valid_from_text ?></td>
                            <td><?= $ofp->valid_to_text ?></td>
                            <td><?= Html::a($ofp->person->dispname, ['/person/view', 'id' => $ofp->person_id],
                                ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $ofp->note ?></td>
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
                    <th>TO</th>
                    <th>耕作者</th>
                    <th>メモ</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->cultivatorFieldPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>現在</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->cultivatorFieldPersons as $cfp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $cfp->valid_from_text ?></td>
                            <td><?= $cfp->valid_to_text ?></td>
                            <td><?= Html::a($cfp->person->dispname, ['/person/view', 'id' => $cfp->person_id],
                                        ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                            <td><?= $cfp->note ?></td>
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
                    <th>TO</th>
                    <th>利用状況</th>
                    <th>メモ</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->fieldUsages) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>現在</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->fieldUsages as $fo): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $fo->valid_from_text ?></td>
                            <td><?= $fo->valid_to_text ?></td>
                            <td><?= $fo->usage->name ?></td>
                            <td><?= $fo->note ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
        </div>
    </div>
</div>