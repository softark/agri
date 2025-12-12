<?php

use app\models\Icon;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'ユーザ : ' . $model->longname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => 'ユーザ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->longname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="user-update">

    <h1><?= Icon::getIcon('update') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
