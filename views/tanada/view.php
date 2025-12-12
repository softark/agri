<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tanada $model */

$this->title = '棚田 : ' . $model->p_no . ' (' . $model->owner . ')';
$this->params['breadcrumbs'][] = ['label' => '棚田', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tanada-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        // 'id',
                        // 'geom',
                            'p_no',
                            'owner',
                            [
                                    'attribute' => 'area',
                                    'value' => sprintf('%.02f', $model->area),
                            ],
                            'cultivator',
                            'usage',
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>

</div>
