<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */
/** @var string|array $ret_route */

$this->title = '山林 : ' . $model->title . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="forest-update">

    <h1><?= Icon::getIconAndLabel('tree') . ' : ' . $model->title . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
