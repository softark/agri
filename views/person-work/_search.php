<?php

use app\components\Icon;
use app\models\PersonWork;
use app\models\PersonWorkSearch;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonWorkSearch $model */

/** @var yii\bootstrap5\ActiveForm $form */

use app\assets\SearchFormAsset;

SearchFormAsset::register($this);
?>

<div class="person-work-search">

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
            <?= $form->field($model, 'src')->dropDownList(PersonWork::getSrcTypes(), ['prompt' => '']) ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'address') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($model, 'p_link')
                    ->dropDownList(PersonWorkSearch::getStatusList()) ?>
        </div>

        <div class="form-group search-buttons col-lg-2 col-md-3 col-sm-3 col-4">
            <p class="text-nowrap pt-2">
                <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm', 'data-clear' => 1]) ?>
            </p>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
