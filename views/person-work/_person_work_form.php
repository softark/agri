<?php

use app\components\Icon;
use app\models\Person;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonWorkForm $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string $route */

\app\assets\JuiAsset::register($this);

$this->registerJs("
// 検索データ受信時の処理
function zipDataReceive(response, data) {
    response($.map(data, function (item) {
        // 住所
        var address = item.pref + item.town + item.block;
        // ラベル
        var label = item.zip_code + ' : ' + address;
        if (item.street) {
            label += ' (' + item.street + ')';
        }
        return {
            label: label,
            zip_code: item.zip_code,
            address: address,
        }
    }));
}

// フォームの項目を更新
function zipDataUpdate(ui) {
    $('#zip').val(ui.item.zip_code);
    $('#address1').val(ui.item.address);
}

// 郵便番号の入力フィールドに Autocomplete を適用
$('#zip').autocomplete({
    delay: 500,
    minLength: 3,
    source: function (request, response) {
        $.ajax({
            url: 'https://tools.softark.net/zipdata/api/search',
            dataType: 'jsonp',
            data: {
                mode: 0,
                term: request.term,
                max_rows: 100,
                biz_mode: 0,
                sort: 0
            },
            success: function (data) {
                zipDataReceive(response, data);
            }
        });
    },
    select: function (event, ui) {
        zipDataUpdate(ui);
        return false;
    }
});

// 住所の入力フィールドに Autocomplete を適用
$('#address1').autocomplete({
    delay: 300,
    minLength: 2,
    source: function (request, response) {
        $.ajax({
            url: 'https://tools.softark.net/zipdata/api/search',
            dataType: 'jsonp',
            data: {
                mode: 1,
                term: request.term,
                max_rows: 100,
                biz_mode: 0,
                sort: 1
            },
            success: function (data) {
                zipDataReceive(response, data);
            }
        });
    },
    select: function (event, ui) {
        zipDataUpdate(ui);
        return false;
    }
});
");
?>

<div class="person-work-form">
    <div class="row">
        <div class="col-lg-5">

            <?php $form = ActiveForm::begin([
                    'id' => 'person-work-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <?= $form->field($model, 'type')->dropDownList(Person::getTypes()) ?>
            <?= $form->field($model, 'name1')->textInput(['maxlength' => true, 'autocomplete' => 'new_name1']) ?>
            <?= $form->field($model, 'name2')->textInput(['maxlength' => true, 'autocomplete' => 'new_name2']) ?>
            <?= $form->field($model, 'yomi1')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi1']) ?>
            <?= $form->field($model, 'yomi2')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi2']) ?>
            <?= $form->field($model, 'person_note')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'has_contact')->checkbox() ?>
            <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'id' => 'zip']) ?>
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true, 'id' => 'address1']) ?>
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true,]) ?>
            <?= $form->field($model, 'contact_note')->textInput(['maxlength' => true]) ?>
            <div class="form-group">
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
