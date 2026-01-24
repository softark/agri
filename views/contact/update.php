<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */
/** @var string|array $ret_route */

$this->title = $model->disp_name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->disp_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIconAndLabel('contact') . ' : ' . $model->disp_name . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
