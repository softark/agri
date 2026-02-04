<?php

use app\components\Icon;
use app\models\Usage;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\UsageSearch $model */

/** @var yii\bootstrap5\ActiveForm $form */

use app\assets\SearchFormAsset;

SearchFormAsset::register($this);
?>

<div class="usage-search">

    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'data-search-form' => 1,
            ],
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
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'data-clear' => 1]) ?>
            </p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
