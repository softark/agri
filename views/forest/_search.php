<?php

use app\models\Aza;
use app\models\Frtype;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ForestSearch $model */
/** @var yii\bootstrap5\ActiveForm $form */

$this->registerJs("
$('#forest-search-form').on('click', '#clear-btn', function(event){
    $('#forest-search-form select').val('');
    $('#forest-search-form input').val('');
    $('#forest-search-form').submit();
    event.preventDefault();
});
$('#forest-search-form').on('change', 'select', function(event){
    $('#forest-search-form').submit();
    event.preventDefault();
});
$('#forest-search-form').on('change', 'input', function(event){
    $('#forest-search-form').submit();
    event.preventDefault();
});
");
?>

<div class="forest-search">
    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'autocomplete' => 'off'
            ],
            'id' => 'forest-search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => ''])    ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'p_no') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'type_id')->dropDownList(Frtype::getTypeList(), ['prompt' => ''])    ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'search_name') ?>
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
