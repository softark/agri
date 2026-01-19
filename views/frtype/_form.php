<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Frtype $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>

<div class="frtype-form">

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                    'id' => 'frtype-edit-form',
                    'enableAjaxValidation' => false,
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ]
            ]); ?>

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
