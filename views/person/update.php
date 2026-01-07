<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonForm $model */
/** @var string|array $ret_route */

$this->title = '名簿 : ' . $model->dispname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->person->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIcon('update') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_person_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
