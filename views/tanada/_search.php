<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TanadaSearch $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJs("
$('#tanada-search-form').on('click', '#clear-btn', function(event){
    $('#tanada-search-form select').val('');
    $('#tanada-search-form input').val('');
    $('#tanada-search-form').submit();
    event.preventDefault();
});
$('#tanada-search-form').on('change', 'select', function(event){
    $('#tanada-search-form').submit();
    event.preventDefault();
});
$('#tanada-search-form').on('change', 'input', function(event){
    $('#tanada-search-form').submit();
    event.preventDefault();
});
");

?>

<div class="tanada-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1,
            'autocomplete' => 'off'
        ],
        'id' => 'tanada-search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'p_no') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'owner') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?php echo $form->field($model, 'cultivator') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?php echo $form->field($model, 'usage') ?>
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
