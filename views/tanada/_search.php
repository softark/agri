<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TanadaSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tanada-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'geom') ?>

    <?= $form->field($model, 'area') ?>

    <?= $form->field($model, 'p_no') ?>

    <?= $form->field($model, 'owner') ?>

    <?php // echo $form->field($model, 'cultivator') ?>

    <?php // echo $form->field($model, 'usage') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
