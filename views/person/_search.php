<?php

use app\models\Icon;
use app\models\Person;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#person-search-form').on('click', '#clear-btn', function(event){
    $('#person-search-form select').val('');
    $('#person-search-form input').val('');
    $('#person-search-form').submit();
    event.preventDefault();
});
$('#person-search-form').on('change', 'select', function(event){
    $('#person-search-form').submit();
    event.preventDefault();
});
$('#person-search-form').on('change', 'input', function(event){
    $('#person-search-form').submit();
    event.preventDefault();
});
");
?>

<div class="person-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'autocomplete' => 'off'
            ],
            'id' => 'person-search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'status')->dropDownList(Person::getStates(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'type')->dropDownList(Person::getTypes(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_address') ?>
        </div>

        <div class="form-group search-buttons col-md-2 col-sm-3 col-4">
            <p class="text-nowrap pt-2">
                <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'id' => 'clear-btn']) ?>
            </p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
