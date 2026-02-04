<?php

use app\components\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var app\models\Contact $contact */

\app\assets\ZipSearchAsset::register($this);

$this->registerJs("
// 連絡先選択ダイアログのポップアップ
$(document).on('click', '#btn-contact-select', function(event){
  $('#contact-select-modal').modal('show');
  event.preventDefault();
});
");
?>

<div class="contact-form" id="contact-form">
    <div class="row">
        <div class="col-lg-5">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            [
                                    'attribute' => 'type',
                                    'value' => function ($model) {
                                        return $model->typeText;
                                    },
                            ],
                            'dispname',
                            'yomigana',
                            'note',
                    ],
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
            <p>関係者と連絡先の名前が同じ場合は、役割／肩書、連絡先名前半、連絡先名後半は省略して、郵便番号以下を入力してください</p>
            <?= $form->field($contact, 'order')->textInput(['disabled' => true]) ?>
            <?= $form->field($contact, 'role')->textInput(['maxlength' => true, 'id' => 'role']) ?>
            <?= $form->field($contact, 'name1')->textInput(['maxlength' => true, 'id' => 'contact-name1']) ?>
            <?= $form->field($contact, 'name2')->textInput(['maxlength' => true, 'id' => 'contact-name2']) ?>
            <?= $form->field($contact, 'zip')->textInput(['maxlength' => true, 'id' => 'zip',
                    'data-zip-autocomplete' => true, 'data-zip-target' => '#zip', 'data-address-target' => '#address1']) ?>
            <?= $form->field($contact, 'address1')->textInput(['maxlength' => true, 'id' => 'address1',
                    'data-address-autocomplete' => true, 'data-zip-target' => '#zip', 'data-address-target' => '#address1']) ?>
            <?= $form->field($contact, 'address2')->textInput(['maxlength' => true, 'id' => 'address2']) ?>
            <?= $form->field($contact, 'phone1')->textInput(['maxlength' => true, 'id' => 'phone1']) ?>
            <?= $form->field($contact, 'phone2')->textInput(['maxlength' => true, 'id' => 'phone2']) ?>
            <?= $form->field($contact, 'mail')->textInput(['maxlength' => true, 'id' => 'mail']) ?>
            <?= $form->field($contact, 'note')->textInput(['maxlength' => true, 'id' => 'contact-note']) ?>

            <div class="form-group">
                <?php if ($contact->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'),
                        ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
            <?= $this->render('/contact/_select_modal.php', [
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
        <?php if (count($contact->person->contacts) > 1): ?>
            <p>※ 連絡先の優先順位の変更は、関係者の閲覧画面で行うことが出来ます。
                <?= Html::a($contact->person->dispname,
                        ['/person/view', 'id' => $contact->person->id],
                        ['class' => 'btn btn-outline-primary']) ?>
            </p>
        <?php endif; ?>
    </div>
</div>
