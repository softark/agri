<?php

use app\models\Forest;
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
                // 'id',
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
                                return Forest::getAreaTextFull($model->area);
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
                <?php if (count($model->ownerForestPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>現在</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->ownerForestPersons as $ofp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $ofp->valid_from_text ?></td>
                            <td><?= $ofp->valid_to_text ?></td>
                            <td><?= $ofp->person->dispname ?></td>
                            <td><?= $ofp->note ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <h2 class="h5">管理者</h2>
            <table class="table table-striped table-bordered table-sm">
                <thead>
                <tr>
                    <th>#</th>
                    <th>FROM</th>
                    <th>TO</th>
                    <th>管理者</th>
                    <th>メモ</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->managerForestPersons) == 0): ?>
                    <tr>
                        <td>1</td>
                        <td>****</td>
                        <td>現在</td>
                        <td>未登録</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php else: ?>
                    <?php $n = 1; ?>
                    <?php foreach ($model->managerForestPersons as $mfp): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= $mfp->valid_from_text ?></td>
                            <td><?= $mfp->valid_to_text ?></td>
                            <td><?= $mfp->person->dispname ?></td>
                            <td><?= $mfp->note ?></td>
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
