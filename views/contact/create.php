<?php

use app\models\Icon;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

$this->title = '連絡先を新規登録';
$this->params['breadcrumbs'][] = ['label' => '連絡先', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-create">

    <h1><?= Icon::getIconAndLabel('contact') . 'を新規登録' ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
