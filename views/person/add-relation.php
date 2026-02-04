<?php

use app\components\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var app\models\PersonRelation $relation */
/** @var string $mode */

$modeText = ($mode == 'A') ? '引継元' : '引継先';
$this->title = '関係者 : ' . $model->dispname . ' - ' . $modeText . 'を登録';
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $modeText . 'を登録';

?>
    <div class="add-relation">

        <h1><?= Icon::getIcon('contact') . ' 関係者 : ' . $model->dispname . ' - ' . Icon::getIcon('update') . ' ' . $modeText . 'を登録' ?></h1>

        <div class="row">
            <div class="col-lg-5 col-md-8">
                <?php $form = ActiveForm::begin([
                        'id' => 'relation-add-form',
                        'enableAjaxValidation' => false,
                        'options' => ['autocomplete' => 'off'],
                        'fieldConfig' => [
                                'options' => ['class' => 'mb-3'],
                                'errorOptions' => ['class' => 'd-none'],
                        ],
                ]); ?>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($relation, 'from_person_id')->textInput(['id' => 'from-person-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <p class="form-control mt-2"
                           id="from-person-name"><?= $mode == 'A' ? '&nbsp;' : $model->dispname ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-from-person', 'disabled' => $mode == 'D']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($relation, 'to_person_id')->textInput(['id' => 'to-person-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <p class="form-control mt-2"
                           id="to-person-name"><?= $mode == 'D' ? '&nbsp;' : $model->dispname ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-to-person', 'disabled' => $mode == 'A']) ?>
                    </div>
                </div>
                <?= $form->field($relation, 'note')->textInput(['maxlength' => true]) ?>
                <?= $form->errorSummary($relation); ?>

                <div class="form-group">
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                    <?= Html::a(Icon::getIconAndLabel('cancel'), ['update-relation', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

    </div>

<?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
<?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
<?= $this->render('/person/_select_modal.php', [
        'modalId' => 'person-select-modal',
        'pickerMap' => [
                'person-id' => '#person-id',
                'person-name' => '#person-name',
        ],
]);
?>

<?php $this->registerJs("
var mode = 0;
$(document).on('click', '#btn-from-person', function(event){
  mode = 0;
  $('#person-select-modal').modal('show');
  event.preventDefault();
});
$(document).on('click', '#btn-to-person', function(event){
  mode = 1;
  $('#person-select-modal').modal('show');
  event.preventDefault();
});
$(document).on('picker:selected', '#person-select-modal', function() {
  event.preventDefault();
  if (mode == 0) {
    $('#from-person-id').val($('#person-id').val());
    $('#from-person-name').text($('#person-name').val());
  } else {
    $('#to-person-id').val($('#person-id').val());
    $('#to-person-name').text($('#person-name').val());
  }
});
");