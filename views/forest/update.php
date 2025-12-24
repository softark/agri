<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */

$this->title = '山林 : ' . $model->p_no . ' (' . $model->owner . ') - 編集';
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->p_no . ' (' . $model->owner . ')', 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="forest-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
