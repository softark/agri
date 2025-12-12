<?php

use app\models\Icon;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'ユーザを新規登録';
$this->params['breadcrumbs'][] = ['label' => 'ユーザ', 'url' => ['index']];
$this->params['breadcrumbs'][] = '新規登録';
?>
<div class="user-create">

    <h1><?= Icon::getIconAndLabel('user-create') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
