<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = '名簿に新規登録';
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Icon::getIconAndLabel('person') . ' に新規登録' ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
