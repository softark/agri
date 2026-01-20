<?php

use app\models\Aza;
use app\models\Icon;
use app\models\Usage;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\FieldSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#field-search-form').on('click', '#clear-btn', function(event){
    $('#field-search-form select').val('');
    $('#field-search-form input').val('');
    $('#field-search-form').submit();
    event.preventDefault();
});
$('#field-search-form').on('change', 'select', function(event){
    $('#field-search-form').submit();
    event.preventDefault();
});
$('#field-search-form').on('change', 'input', function(event){
    $('#field-search-form').submit();
    event.preventDefault();
});
");
?>

<div class="field-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1
            ],
            'id' => 'field-search-form',
    ]); ?>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'p_no') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_usage')->dropDownList(Usage::getTypeAndUsageList(), ['prompt' => '']) ?>
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
    </div>
    <?php ActiveForm::end(); ?>

</div>
