<?php

use app\models\FieldPerson;
use app\models\Icon;
use yii\bootstrap5\ActiveForm;use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\FieldPerson $model */
/** @var app\models\Field $field */
/** @var string|array $ret_route */

$name = ' [' . $model->usage->name . '] ';
$this->title = '農地 : ' . $field->p_no . ' - ' . $name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $field->p_no, 'url' => ['view', 'id' => $field->id]];
$this->params['breadcrumbs'][] = $name . '編集';
?>
<div class="field-update">

    <h1><?= Icon::getIconAndLabel('field') . ' : ' . $field->p_no . ' - ' .
        Icon::getIcon('update') .  ' ' . $name . ' - 編集'?></h1>

    <?= $this->render('_fu_form', [
            'model' => $model,
            'field' => $field,
            'ret_route' => $ret_route]
            ) ?>
