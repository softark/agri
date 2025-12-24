<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="forest-form">
    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                    'id' => 'forest-edit-form',
                    'enableAjaxValidation' => true,
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ]
            ]); ?>
            <?= $form->field($model, 'p_no')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'o_aza')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'ko_aza')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'owner')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'o_addr')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'm_addr')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'manager')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'area')->textInput(['disabled' => true]) ?>

            <?= $form->field($model, 'memo')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'o_type')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']), ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
