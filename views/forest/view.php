<?php

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
                    <?php if (\yii::$app->user->can('forest.edit', ['id' => $model->id])) : ?>
                        <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
                    <?php endif; ?>
                    <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                </p>

                <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
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
                                        'attribute' => 'owner_id',
                                        'value' => function ($model) {
                                            return $model->owner_name;
                                        },
                                ],
                                [
                                        'attribute' => 'manager_id',
                                        'value' => function ($model) {
                                            return $model->manager_name;
                                        },
                                ],
                                [
                                        'attribute' => 'area',
                                        'value' => function ($model) {
                                            return number_format($model->area, 2);
                                        },
                                ],
                                'note',
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
                <p>
                    <?= Html::a(Icon::getIcon('map-location') . ' i-GIS で見る', $model->mapurl, ['class' => 'btn btn-outline-primary', 'target' => '_blank']) ?>
                </p>
            </div>
            <div class="col-lg-8 col-md-6">
                <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
            </div>
        </div>
    </div>
