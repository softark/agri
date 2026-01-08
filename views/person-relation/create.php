<?php

use app\models\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonRelation $model */
/** @var string|array $ret_route */

$this->title = '引継を登録する';
$this->params['breadcrumbs'][] = ['label' => '引継', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-relation-create">

    <h1><?= Icon::getIconAndLabel('succeed') . ' を登録する' ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'ret_route' => $ret_route,
    ]) ?>

</div>
