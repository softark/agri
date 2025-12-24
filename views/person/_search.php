<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $model */
/** @var yii\widgets\ActiveForm $form */

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
            <?= $form->field($model, 'search_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'address') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_phone') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'memo') ?>
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
