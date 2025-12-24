<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = 'Update Person Work: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Person Works', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="person-work-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
