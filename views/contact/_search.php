<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ContactSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#contact-search-form').on('click', '#clear-btn', function(event){
    $('#contact-search-form select').val('');
    $('#contact-search-form input').val('');
    $('#contact-search-form').submit();
    event.preventDefault();
});
$('#contact-search-form').on('change', 'select', function(event){
    $('#contact-search-form').submit();
    event.preventDefault();
});
$('#contact-search-form').on('change', 'input', function(event){
    $('#contact-search-form').submit();
    event.preventDefault();
});
");
?>

<div class="contact-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'autocomplete' => 'off'
            ],
            'id' => 'contact-search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'address1') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_phone') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'note') ?>
        </div>

        <div class="form-group search-buttons col-md-3 col-sm-3 col-4">
            <p class="text-nowrap pt-2">
                <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'id' => 'clear-btn']) ?>
            </p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
