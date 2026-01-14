<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Frtype $model */
/** @var string|array $ret_route */

$this->title = '山林タイプ : ' . $model->name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '山林タイプ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="frtype-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'ret_route' => $ret_route,
    ]) ?>

</div>
