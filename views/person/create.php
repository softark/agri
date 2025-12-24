<?php

use app\models\Icon;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = '住所カードを新規登録';
$this->params['breadcrumbs'][] = ['label' => '住所録', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Icon::getIconAndLabel('address-card') . 'を新規登録' ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
