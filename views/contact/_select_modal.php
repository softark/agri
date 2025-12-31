<?php

/* @var $this yii\web\View */
/* @var $personIdInput string */

/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\Icon;
use app\models\PersonSearch;
use yii\bootstrap5\Html;
use yii\bootstrap5\Modal;
use yii\widgets\ActiveForm;

$this->registerCss("
.person-row.is-selected td {
  background: #0d6efd; /* Bootstrap3なら info 系 */
  color: white;
");

$searchModel = new PersonSearch(['_form_name' => 'psel']);
$dataProvider = $searchModel->search([], 10);

Modal::begin([
        'title' => '住所カードを選択',
        'toggleButton' => false,
        'id' => 'person-select-modal',
        'size' => Modal::SIZE_EXTRA_LARGE,
]);
?>

    <div class="form-group float-end">
        <?= Html::button(Icon::getIconAndLabel('ok', '選択'), ['id' => 'modal-ok', 'class' => 'disabled btn btn-primary']) ?>
        <?= Html::button(Icon::getIconAndLabel('cancel'), ['id' => 'modal-cancel', 'class' => 'btn btn-outline-secondary']) ?>
    </div>

<?php $form = ActiveForm::begin([
        'id' => 'person-search-form',
        'method' => 'get',
        'action' => ['/person/select'],
        'fieldConfig' => [
                'inputOptions' => ['class' => 'allow_submit form-control']
        ],
]); ?>
    <div class="row">
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'search_name') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'address') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'search_phone') ?>
        </div>
        <div class="col-md-2 col-sm-3 col-5">
            <?= $form->field($searchModel, 'memo') ?>
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
function openPersonSelectModal() {
    updatePersonSelectList();
    $('#person-select-modal').modal('show');
}
$('#person-search-form-modal').on('change', 'select', function(event){
    updatePersonSelectList();
    event.preventDefault();
});
$('#person-search-form').on('change', 'input', function(event){
    updatePersonSelectList();
    event.preventDefault();
});
$('#person-search-form').on('click', '#clear-btn', function(event){
    $('#person-search-form input:text').val('');
    $('#person-search-form input:checked').prop('checked', false);
    $('#person-search-form select').val('');
    updatePersonSelectList();
    event.preventDefault();
});
function updatePersonSelectList() {
    $('#person-search-form').submit();
}
$('#person-select-modal').on('click', '.person-row', function(e){
  $('.person-row.is-selected').removeClass('is-selected');
  $(this).addClass('is-selected');

  const id = $(this).data('person-id');
  $('#$personIdInput').val(id); // hidden or input
  $('#modal-ok').removeClass('disabled');
});
$('#modal-ok').on('click', function(event){
  event.preventDefault();
  $('#$personIdInput').trigger('change');
  $('#person-select-modal').modal('hide');
  $('#modal-ok').addClass('disabled');
});
$('#modal-cancel').on('click', function(event){
  event.preventDefault();
  $('#person-select-modal').modal('hide');
  $('#modal-ok').addClass('disabled');
});
");
