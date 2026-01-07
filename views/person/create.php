<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonForm $model */
/** @var string|array $ret_route */

$this->title = '名簿に新規登録';
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Icon::getIconAndLabel('person') . ' に新規登録' ?></h1>

    <?= $this->render('_person_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
