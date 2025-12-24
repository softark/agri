<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = 'Create Person Work';
$this->params['breadcrumbs'][] = ['label' => 'Person Works', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-work-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
