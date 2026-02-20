<?php

use app\components\Icon;
use app\models\Aza;
use app\models\Frtype;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\ForestSearch $model */

/** @var yii\bootstrap5\ActiveForm $form */

use app\assets\SearchFormAsset;

SearchFormAsset::register($this);
?>

<div class="forest-search">
    <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                    'data-pjax' => 1,
                    'autocomplete' => 'off',
                    'data-search-form' => 1,
            ],
    ]); ?>

    <div class="row">
        <div class="col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
        </div>
        <div class="col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'type_id')->dropDownList(Frtype::getTypeList(), ['prompt' => '']) ?>
        </div>
        <div class="col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'search_name') ?>
        </div>
        <div class="col-xl-1 col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'owner_name') ?>
        </div>
        <div class="col-xl-1 col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'manager_name') ?>
        </div>
        <div class="col-lg-2 col-sm-3 col-4">
            <?= $form->field($model, 'note') ?>
        </div>

        <div class="form-group search-buttons col-md-2 col-sm-3 col-4">
            <p class="text-nowrap pt-2">
                <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'data-clear' => 1]) ?>
            </p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
