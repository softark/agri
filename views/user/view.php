<?php

use app\models\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'ユーザ : ' . $model->longname;
$this->params['breadcrumbs'][] = ['label' => 'ユーザ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->longname;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Icon::getIcon('user') . ' ' . Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-6">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'username',
                            'dispname',
                            'created_at',
                            [
                                    'attribute' => 'created_by',
                                    'value' => function ($model) {
                                        return $model->createdBy->longname;
                                    }
                            ],
                            'updated_at',
                            [
                                    'attribute' => 'updated_by',
                                    'value' => function ($model) {
                                        return $model->updatedBy->longname;
                                    }
                            ],
                    ],
            ]) ?>

            <p>
                <?php if (\yii::$app->user->can('user.edit', ['id' => $model->id])
                        && $model->id >= User::USER_NORMAL_START) : ?>
                    <?= Html::a(Icon::getIconAndLabel('update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
                <?php if (\yii::$app->user->can('user.delete')
                        && $model->id >= User::USER_NORMAL_START) : ?>
                    <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => 'ユーザ <strong>"' . $model->longname . '"</strong> を削除しますか？',
                                    'method' => 'post',
                            ],
                    ]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>
        </div>
    </div>

</div>
