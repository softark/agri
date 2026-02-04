<?php

use app\components\Icon;
use app\models\FieldPerson;

/** @var yii\web\View $this */
/** @var app\models\FieldPerson $model */
/** @var app\models\Field $field */
/** @var string|array $ret_route */

$role_text = $model->role == FieldPerson::ROLE_OWNER ? '所有者' : '耕作者';
$name = ' [' . $model->person->dispname . '] ';
$this->title = '農地 : ' . $field->p_no . ' / ' . $role_text . $name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $field->p_no, 'url' => ['view', 'id' => $field->id]];
$this->params['breadcrumbs'][] = $role_text . $name . ' - 編集';
?>
<div class="field-update">

    <h1><?= Icon::getIconAndLabel('field') . ' : ' . $field->p_no . ' / ' . $role_text . $name .
        ' - ' . Icon::getIconAndLabel('update')?></h1>

    <?= $this->render('_fp_form', [
            'model' => $model,
            'field' => $field,
            'ret_route' => $ret_route]
            ) ?>
