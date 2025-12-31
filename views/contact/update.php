<?php

use app\models\Icon;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */

$this->title = '連絡先 : ' . $model->address . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->address, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-update">

    <h1><?= Icon::getIcon('update') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
