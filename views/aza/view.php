<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Aza $model */

$this->title = '字（あざ） : ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '字（あざ）', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="aza-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id, 'ret_route' => ['view', 'id' => $model->id]], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                        'confirm' => 'この字（あざ）を削除しても構いませんか？',
                        'method' => 'post',
                ],
        ]) ?>
        <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        // 'id',
                            'name',
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
        </div>
    </div>

</div>
