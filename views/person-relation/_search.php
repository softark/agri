<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PersonRelationSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#pr-search-form').on('click', '#clear-btn', function(event){
    $('#pr-search-form select').val('');
    $('#pr-search-form input').val('');
    $('#pr-search-form').submit();
    event.preventDefault();
});
$('#pr-search-form').on('change', 'select', function(event){
    $('#pr-search-form').submit();
    event.preventDefault();
});
$('#pr-search-form').on('change', 'input', function(event){
    $('#pr-search-form').submit();
    event.preventDefault();
});
");
?>

<div class="person-relation-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'autocomplete' => 'off'
            ],
            'id' => 'pr-search-form',
    ]); ?>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'from_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'to_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'note') ?>
        </div>

        <div class="form-group search-buttons col-md-2 col-sm-3 col-4">
            <p class="text-nowrap pt-2">
                <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'id' => 'clear-btn']) ?>
            </p>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
