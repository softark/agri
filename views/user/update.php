<?php

use app\components\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'ユーザ : ' . $model->longname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => 'ユーザ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->longname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="user-update">

    <h1><?= Icon::getIcon('user') . ' ' . Html::encode($model->longname) . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
