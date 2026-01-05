<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\models\Person;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */
/** @var app\models\Person $person */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var $route */

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
            <?= $form->field($person, 'type')->dropDownList(Person::getTypes()) ?>
            <?= $form->field($person, 'name1')->textInput(['maxlength' => true, 'autocomplete' => 'new_name1']) ?>
            <?= $form->field($person, 'name2')->textInput(['maxlength' => true, 'autocomplete' => 'new_name2']) ?>
            <?= $form->field($person, 'yomi1')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi1']) ?>
            <?= $form->field($person, 'yomi2')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi2']) ?>
            <?= $form->field($person, 'note')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
