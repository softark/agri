<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */
/** @var string|array $ret_route */

$this->title = '連絡先 : ' . $model->contactname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->contactname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIconAndLabel('contact') . ' : ' . $model->contactname . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
