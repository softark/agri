<?php

use app\models\Icon;
use app\models\Usage;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\UsageSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#usage-search-form').on('click', '#clear-btn', function(event){
    $('#usage-search-form select').val('');
    $('#usage-search-form input').val('');
    $('#usage-search-form').submit();
    event.preventDefault();
});
$('#usage-search-form').on('change', 'select', function(event){
    $('#usage-search-form').submit();
    event.preventDefault();
});
$('#usage-search-form').on('change', 'input', function(event){
    $('#usage-search-form').submit();
    event.preventDefault();
});
");

?>

<div class="usage-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1
            ],
            'id' => 'usage-search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'type')->dropDownList(Usage::getTypes(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'name') ?>
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
