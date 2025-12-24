<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '住所録辞書', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="person-work-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'id',
                            'name',
                            'address',
                            'person_id',
                    ],
            ]) ?>

            <p>
                <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                                'confirm' => '住所録辞書エントリ <strong>"' . $model->name . '"</strong> を削除しますか？',
                                'method' => 'post',
                        ],
                ]) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>
</div>
