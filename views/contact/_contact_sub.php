<?php

use app\components\Icon;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var app\models\Contact $model */

\app\assets\ZipSearchAsset::register($this);

$this->registerJs("
// 連絡先選択ダイアログのポップアップ
$(document).on('click', '#btn-contact-select', function(event){
  $('#contact-select-modal').modal('show');
  event.preventDefault();
});
");
?>

<p><?= Html::button(Icon::getIcon('contact') . ' 既存の連絡先をコピー', ['id' => 'btn-contact-select', 'class' => 'btn btn-outline-secondary']) ?></p>
<p>関係者と連絡先の名前が同じ場合は、役割／肩書、連絡先名前半、連絡先名後半は省略して、郵便番号以下を入力してください</p>
<?= $form->field($model, 'order')->textInput(['disabled' => true]) ?>
<?= $form->field($model, 'role')->textInput(['maxlength' => true, 'id' => 'role']) ?>
<?= $form->field($model, 'name1')->textInput(['maxlength' => true, 'id' => 'contact-name1']) ?>
<?= $form->field($model, 'name2')->textInput(['maxlength' => true, 'id' => 'contact-name2']) ?>
<?= $form->field($model, 'zip')->textInput(['maxlength' => true, 'id' => 'zip',
        'data-zip-autocomplete' => true, 'data-zip-target' => '#zip', 'data-address-target' => '#address1']) ?>
<?= $form->field($model, 'address1')->textInput(['maxlength' => true, 'id' => 'address1',
        'data-address-autocomplete' => true, 'data-zip-target' => '#zip', 'data-address-target' => '#address1']) ?>
<?= $form->field($model, 'address2')->textInput(['maxlength' => true, 'id' => 'address2']) ?>
<?= $form->field($model, 'phone1')->textInput(['maxlength' => true, 'id' => 'phone1']) ?>
<?= $form->field($model, 'phone2')->textInput(['maxlength' => true, 'id' => 'phone2']) ?>
<?= $form->field($model, 'mail')->textInput(['maxlength' => true, 'id' => 'mail']) ?>
<?= $form->field($model, 'note')->textInput(['maxlength' => true, 'id' => 'contact-note']) ?>
