<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var yii\widgets\ActiveForm $form */

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
    $('#address').val(ui.item.address);
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
$('#address').autocomplete({
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

<div class="person-form">
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
                    'id' => 'person-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <?= $form->field($model, 'name1')->textInput(['maxlength' => true, 'autocomplete' => 'new_name1']) ?>
            <?= $form->field($model, 'name2')->textInput(['maxlength' => true, 'autocomplete' => 'new_name2']) ?>
            <?= $form->field($model, 'yomi1')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi1']) ?>
            <?= $form->field($model, 'yomi2')->textInput(['maxlength' => true, 'autocomplete' => 'new_yomi2']) ?>
            <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'id' => 'zip']) ?>
            <?= $form->field($model, 'address')->textInput(['maxlength' => true, 'id' => 'address']) ?>
            <?= $form->field($model, 'phone1')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'memo')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']), ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
