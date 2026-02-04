<?php

use app\components\Icon;

/** @var yii\web\View $this */
/** @var app\models\FieldPerson $model */
/** @var app\models\Field $field */
/** @var string|array $ret_route */

$this->title = '農地 : ' . $field->p_no . ' / ' . '利用状況を新規登録';
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $field->p_no, 'url' => ['view', 'id' => $field->id]];
$this->params['breadcrumbs'][] = '利用状況を新規登録';
?>
<div class="forest-update">

    <h1><?= Icon::getIconAndLabel('field') . ' : ' . $field->p_no . ' / '  . '利用状況を新規登録' ?></h1>

    <?= $this->render('_fu_form', [
                    'model' => $model,
                    'field' => $field,
                    'ret_route' => $ret_route]
    ) ?>
