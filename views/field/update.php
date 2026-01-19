<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Field $model */
/** @var string|array $ret_route */

$this->title = '農地 : ' . $model->p_no . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->p_no, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="field-update">

    <h1><?= Icon::getIconAndLabel('field') . ' : ' . $model->p_no . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'ret_route' => $ret_route,
    ]) ?>

</div>
