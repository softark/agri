<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */

$this->title = '山林 : ' . $model->p_no . ' (' . $model->owner . ')';
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->p_no . ' (' . $model->owner . ')';
\yii\web\YiiAsset::register($this);
?>
<div class="forest-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'id',
                        // 'geom',
                            'p_no',
                            'o_aza',
                            'ko_aza',
                            'type',
                            'owner',
                            'o_addr',
                            'manager',
                            'm_addr',
                            [
                                    'attribute' => 'area',
                                    'value' => function ($model) {
                                        return number_format($model->area, 2);
                                    }
                            ],
                            'memo',
                            'o_type',
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>

</div>
