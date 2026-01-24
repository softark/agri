<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonForm $model */
/** @var string|array $ret_route */

$this->title = '関係者 : ' . $model->dispname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->person->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIconAndLabel('person') . ' : ' . Html::encode($model->dispname)
        . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_person_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
