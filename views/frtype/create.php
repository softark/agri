<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Frtype $model */
/** @var string|array $ret_route */

$this->title = '山林タイプを新規登録';
$this->params['breadcrumbs'][] = ['label' => '山林タイプ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="frtype-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'model' => $model,
            'ret_route' => $ret_route,
    ]) ?>

</div>
