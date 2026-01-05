<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\Person;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var yii\bootstrap5\ActiveForm $form */

?>

<div class="person-form">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                    'id' => 'person-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <?= $form->field($model, 'type')->dropDownList(Person::getTypes()) ?>
            <?= $form->field($model, 'name1')->textInput(['maxlength' => true, 'autocomplete' => 'new_name1']) ?>
            <?= $form->field($model, 'name2')->textInput(['maxlength' => true, 'autocomplete' => 'new_name2']) ?>
            <?= $form->field($model, 'yomi1')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi1']) ?>
            <?= $form->field($model, 'yomi2')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi2']) ?>
            <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']), ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
