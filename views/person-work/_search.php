<?php

use app\models\Icon;
use app\models\PersonWorkSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PersonWorkSearch $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerJs("
$('#search-form').on('click', '#clear-btn', function(event){
    $('#search-form select').val('');
    $('#search-form input').val('');
    $('#search-form').submit();
    event.preventDefault();
});
$('#search-form').on('change', 'select', function(event){
    $('#search-form').submit();
    event.preventDefault();
});
$('#search-form').on('change', 'input', function(event){
    $('#search-form').submit();
    event.preventDefault();
});
");
?>

<div class="person-work-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
            'id' => 'search-form',
    ]); ?>

    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'address') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'status')
            ->dropDownList(PersonWorkSearch::getStatusList())?>
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
