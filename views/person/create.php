<?php

use app\components\Icon;

/** @var yii\web\View $this */
/** @var app\models\PersonForm $model */
/** @var string|array $ret_route */

$this->title = '関係者を新規登録';
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Icon::getIconAndLabel('person') . ' を新規登録' ?></h1>

    <?= $this->render('_person_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
