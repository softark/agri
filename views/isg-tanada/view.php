<?php

use app\components\Icon;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\IsgTanada $model */

$this->title = '棚田 : ' . $model->p_no . ' (' . $model->owner . ')';
$this->params['breadcrumbs'][] = ['label' => '棚田', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->p_no . ' (' . $model->owner . ')';
\yii\web\YiiAsset::register($this);
?>
<div class="tanada-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'p_no',
                            'owner',
                            'cultivator',
                            'usage',
                            [
                                    'attribute' => 'area',
                                    'value' => number_format($model->area, 2),
                            ],
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>

</div>
