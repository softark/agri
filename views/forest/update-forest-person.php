<?php

use app\models\ForestPerson;
use app\models\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\ForestPerson $model */
/** @var app\models\Forest $forest */
/** @var string|array $ret_route */

$role_text = $model->role == ForestPerson::ROLE_OWNER ? '所有者' : '管理者';
$name = ' [' . $model->person->dispname . '] ';
$this->title = '山林 : ' . $forest->title . ' - ' . $role_text . $name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $forest->title, 'url' => ['view', 'id' => $forest->id]];
$this->params['breadcrumbs'][] = $role_text . $name . ' - 編集';
?>
<div class="forest-update">

    <h1><?= Icon::getIconAndLabel('tree') . ' : ' . $forest->title . ' / ' .
        $role_text . $name . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_fp_form', [
                    'model' => $model,
                    'forest' => $forest,
                    'ret_route' => $ret_route]
    ) ?>
