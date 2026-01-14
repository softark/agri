<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Aza $model */
/** @var string|array $ret_route */

$this->title = '字（あざ）を新規登録';
$this->params['breadcrumbs'][] = ['label' => '字（あざ）', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="aza-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ret_route' => $ret_route,
    ]) ?>

</div>
