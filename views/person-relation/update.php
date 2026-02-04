<?php

use app\components\Icon;

/** @var yii\web\View $this */
/** @var app\models\PersonRelation $model */
/** @var string|array $ret_route */

$this->title = '引継 : ' . $model->fromPerson->dispname . ' > ' . $model->toPerson->dispname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '引継', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fromPerson->dispname . ' > ' . $model->toPerson->dispname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '編集';
?>
<div class="person-relation-update">

    <h1><?= Icon::getIcon('succeed') . ' 引継 : ' . $model->fromPerson->dispname . ' > ' . $model->toPerson->dispname
        . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
