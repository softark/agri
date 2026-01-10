<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\Icon;
use app\models\ContactSearch;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

$this->registerCss("
.contact-row.is-selected td {
  background: #0d6efd; /* Bootstrap3なら info 系 */
  color: white;
");

$searchModel = new ContactSearch(['_form_name' => 'csel']);
$dataProvider = $searchModel->search([], 10);

Modal::begin([
        'title' => '連絡先を選択',
        'toggleButton' => false,
        'id' => 'contact-select-modal',
        'size' => Modal::SIZE_EXTRA_LARGE,
]);
?>

    <div class="form-group float-end">
        <?= Html::button(Icon::getIconAndLabel('ok', '選択'), ['id' => 'modal-ok', 'class' => 'disabled btn btn-primary']) ?>
        <?= Html::button(Icon::getIconAndLabel('cancel'), ['id' => 'modal-cancel', 'class' => 'btn btn-outline-secondary']) ?>
    </div>

<?php $form = ActiveForm::begin([
        'id' => 'contact-search-form',
        'method' => 'get',
        'action' => ['/contact/select'],
        'fieldConfig' => [
                'inputOptions' => ['class' => 'allow_submit form-control']
        ],
]); ?>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'search_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'address1') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'search_phone') ?>
        </div>
        <div class="form-group col-md-2 col-sm-2">
            <?= Html::submitButton(Icon::getBtnText('search'), ['class' => 'btn btn-primary btn-sm d-block']) ?>
            <?= Html::button(Icon::getBtnText('clear'), ['class' => 'btn btn-outline-secondary btn-sm d-block', 'id' => 'clear-btn']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php echo $this->render('_select', ['dataProvider' => $dataProvider]); ?>

<?php
Modal::end();

$this->registerJs("
function openContactSelectModal() {
    updateContactSelectList();
    $('#contact-select-modal').modal('show');
}
$('#contact-search-form').on('change', 'select', function(event){
    updateContactSelectList();
    event.preventDefault();
});
$('#contact-search-form').on('change', 'input', function(event){
    updateContactSelectList();
    event.preventDefault();
});
$('#contact-search-form').on('click', '#clear-btn', function(event){
    $('#contact-search-form input:text').val('');
    $('#contact-search-form input:checked').prop('checked', false);
    $('#contact-search-form select').val('');
    updateContactSelectList();
    event.preventDefault();
});
function updateContactSelectList() {
    $('#contact-search-form').submit();
}

var sel_role;
var sel_name1;
var sel_name2;
var sel_zip;
var sel_address1;
var sel_address2;
var sel_phone1;
var sel_phone2;
var sel_mail;
var sel_note; 

$('#contact-select-modal').on('click', '.contact-row', function(e){
  $('.contact-row.is-selected').removeClass('is-selected');
  $(this).addClass('is-selected');
  sel_role = $(this).data('role');
  sel_name1 = $(this).data('name1');
  sel_name2 = $(this).data('name2');
  sel_zip = $(this).data('zip');
  sel_address1 = $(this).data('address1');
  sel_address2 = $(this).data('address2');
  sel_phone1 = $(this).data('phone1');
  sel_phone2 = $(this).data('phone2');
  sel_mail = $(this).data('mail');
  sel_note = $(this).data('note');    
  $('#modal-ok').removeClass('disabled');
});
$('#modal-ok').on('click', function(event){
  event.preventDefault();
  $('#role').val(sel_role);
  $('#contact-name1').val(sel_name1);
  $('#contact-name2').val(sel_name2);
  $('#zip').val(sel_zip);
  $('#address1').val(sel_address1);
  $('#address2').val(sel_address2);
  $('#phone1').val(sel_phone1);
  $('#phone2').val(sel_phone2);
  $('#mail').val(sel_mail);
  $('#contact-note').val(sel_note);
  $('#contact-select-modal').modal('hide');
  $('#modal-ok').addClass('disabled');
});
$('#modal-cancel').on('click', function(event){
  event.preventDefault();
  $('#contact-select-modal').modal('hide');
  $('#modal-ok').addClass('disabled');
});
");
