<?php

use app\models\Icon;
use app\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'ユーザ一覧';
$this->params['breadcrumbs'][] = 'ユーザ';
?>
<div class="user-index">

    <h1><?= Icon::getIconAndLabel('users') ?></h1>

    <div class="row">
        <div class="col col-lg-8">
            <?php Pjax::begin(); ?>

            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                            ['class' => yii\grid\SerialColumn::class],
                            'username',
                            'dispname',
                            'roleText',
                            [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => Yii::$app->user->can('admin')
                                            ? '{view} {update} {role} {delete}'
                                            : '{view} {update} {delete}',
                                    'buttons' => [
                                            'view' => function ($url, $model, $key) {
                                                return Yii::$app->user->can('user.view', ['id' => $model->id])
                                                        ? Html::a(Icon::getBtnText('view'), $url, ['class' => 'btn btn-primary btn-sm', 'data' => ['pjax' => '0']])
                                                        : '';
                                            },
                                            'update' => function ($url, $model, $key) {
                                                return (Yii::$app->user->can('user.edit', ['id' => $model->id])
                                                        && $model->id >= User::USER_NORMAL_START)
                                                        ? Html::a(Icon::getBtnText('update'), $url, ['class' => 'btn btn-primary btn-sm', 'data' => ['pjax' => '0']])
                                                        : '';
                                            },
                                            'role' => function ($url, $model, $key) {
                                                return (Yii::$app->user->can('user.edit_role')
                                                        && $model->id >= User::USER_NORMAL_START)
                                                        ? Html::a(Icon::getBtnText('role'), $url, ['class' => 'btn btn-warning btn-sm', 'data' => ['pjax' => '0']])
                                                        : '';
                                            },
                                            'delete' => function ($url, $model, $key) {
                                                if (Yii::$app->user->can('user.delete')
                                                        && $model->id >= User::USER_NORMAL_START) {
                                                    return Html::a(Icon::getBtnText('delete'), $url, ['class' => 'btn btn-danger btn-sm', 'data' => [
                                                            'pjax' => '0',
                                                            'confirm' => 'ユーザ ' . ' <strong>"' . $model->longName . '"</strong> を削除しますか?',
                                                            'method' => 'post'
                                                    ]]);
                                                } else {
                                                    return '';
                                                }
                                            },
                                    ]
                            ],
                    ],
            ]); ?>


            <?php Pjax::end(); ?>

        </div>
    </div>
    <?php if (Yii::$app->user->can('user.create')) : ?>
        <?= Html::a(Icon::getIconAndLabel('user-create'), ['create'], ['class' => 'btn btn-success']) ?>
    <?php endif; ?>

</div>
