<?php

use app\models\Aza;
use app\models\Icon;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Field $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>

<div class="field-form">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <?php $form = ActiveForm::begin([
                    'id' => 'field-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>

            <?= $form->field($model, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
            <?= $form->field($model, 'p_no')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'c_area')->textInput(['disabled' => true])->label('地図面積 - 単位は ㎡') ?>
            <?= $form->field($model, 'f_area')->textInput()->label('公称面積 - ㎡ で入力') ?>
            <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
        </div>
    </div>

</div>
