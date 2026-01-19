<?php

use app\models\Icon;
use app\models\Usage;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Usage $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>

<div class="usage-form">
    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                    'id' => 'usage-edit-form',
                    'enableAjaxValidation' => false,
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ]
            ]); ?>
            <?= $form->field($model, 'type')->dropDownList(Usage::getTypes()) ?>
            <?= $form->field($model, 'order')->textInput() ?>
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
