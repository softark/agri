<?php

use app\models\Icon;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = '住所カード : ' . $model->name . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '住所録', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIcon('update') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
