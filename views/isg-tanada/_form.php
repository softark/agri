<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\IsgTanada $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="tanada-form">

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin([
                    'id' => 'tanada-edit-form',
                    'enableAjaxValidation' => true,
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ]
            ]); ?>

            <?= $form->field($model, 'p_no')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'owner')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cultivator')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'usage')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'area')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']), ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
