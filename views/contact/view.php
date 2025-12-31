<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */

$this->title = $model->address;
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-view">

    <h1><?= Icon::getIconAndLabel('contact') . ' : ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'address1',
                            'address2',
                            'phone1',
                            'phone2',
                            'mail',
                            'note',
                            [
                                    'attribute' => 'created_at',
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm:ss');
                                    }
                            ],
                            [
                                    'attribute' => 'created_by',
                                    'value' => function ($model) {
                                        return $model->createdBy->longname;
                                    }
                            ],
                            [
                                    'attribute' => 'updated_at',
                                    'value' => function ($model) {
                                        return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm:ss');
                                    }
                            ],
                            [
                                    'attribute' => 'updated_by',
                                    'value' => function ($model) {
                                        return $model->updatedBy->longname;
                                    }
                            ],
                    ],
            ]) ?>
            <p>
                <?php if (\yii::$app->user->can('contact.edit', ['id' => $model->id])) : ?>
                    <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if (\yii::$app->user->can('contact.delete')) : ?>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '連絡先 <strong>"' . $model->address . '"</strong> を削除しますか？',
                                    'method' => 'post',
                            ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>
</div>
