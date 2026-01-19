<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Usage $model */
/** @var string|array $ret_route */

$this->title = '農地利用状況を新規登録';
$this->params['breadcrumbs'][] = ['label' => '農地利用状況', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'ret_route' => $ret_route,
    ]) ?>

</div>
