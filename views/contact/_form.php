<?php

use app\components\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */

\app\assets\JuiAsset::register($this);

$this->registerJs("
// 連絡先選択ダイアログのポップアップ
$(document).on('click', '#btn-contact-select', function(event){
  $('#contact-select-modal').modal('show');
  event.preventDefault();
});

// ZIP 検索データ受信時の処理
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

<div class="contact-form" id="contact-form">
    <div class="row">
        <div class="col-lg-5">
            <?php
            $attributes = [
                    [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return $model->typeText;
                            },
                    ],
                    'dispname',
                    'yomigana',
            ];
            if ($model->person->note != '') $attributes[] = 'note';
            ?>
            <?= DetailView::widget([
                    'model' => $model->person,
                    'attributes' => $attributes
            ]) ?>
            <p><?= Html::button(Icon::getIcon('contact') . ' 既存の連絡先をコピー', ['id' => 'btn-contact-select', 'class' => 'btn btn-outline-secondary']) ?></p>
            <?php $form = ActiveForm::begin([
                    'id' => 'contact-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <p>関係者と連絡先の名前が同じ場合は、組織名・役割・肩書、宛名（前半）、宛名（後半）は入力する必要はありません</p>
            <?= $form->field($model, 'order')->textInput(['disabled' => true]) ?>
            <?= $form->field($model, 'role')->textInput(['maxlength' => true, 'id' => 'role']) ?>
            <?= $form->field($model, 'name1')->textInput(['maxlength' => true, 'id' => 'contact-name1']) ?>
            <?= $form->field($model, 'name2')->textInput(['maxlength' => true, 'id' => 'contact-name2']) ?>
            <?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'id' => 'zip']) ?>
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true, 'id' => 'address1']) ?>
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true, 'id' => 'address2']) ?>
            <?= $form->field($model, 'phone1')->textInput(['maxlength' => true, 'id' => 'phone1']) ?>
            <?= $form->field($model, 'phone2')->textInput(['maxlength' => true, 'id' => 'phone2']) ?>
            <?= $form->field($model, 'mail')->textInput(['maxlength' => true, 'id' => 'mail']) ?>
            <?= $form->field($model, 'note')->textInput(['maxlength' => true, 'id' => 'contact-note']) ?>

            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
            <?= $this->render('_select_modal.php', [
                    'modalId' => 'contact-select-modal',
                    'pickerMap' => [
                            'role' => '#role',
                            'name1' => '#contact-name1',
                            'name2' => '#contact-name2',
                            'zip' => '#zip',
                            'address1' => '#address1',
                            'address2' => '#address2',
                            'phone1' => '#phone1',
                            'phone2' => '#phone2',
                            'mail' => '#mail',
                            'note' => '#contact-note',
                    ],
            ]); ?>
        </div>
        <?php if (count($model->person->contacts) > 1): ?>
            <p>※ この関係者には複数の連絡先があります。連絡先の優先順位の変更は、関係者の閲覧画面で行うことが出来ます。
                <?= Html::a(Icon::getIcon('view') . ' ' . $model->person->dispname,
                        ['/person/view', 'id' => $model->person->id],
                        ['class' => 'btn btn-outline-primary']) ?>
            </p>
        <?php endif; ?>
    </div>
</div>
