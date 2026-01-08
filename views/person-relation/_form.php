<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PersonRelation $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>

<div class="person-relation-form">

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
            <div class="row">
                <div class="col-4">
                    <?= $form->field($model, 'from_person_id')->textInput(['id' => 'from-person-id', 'readonly' => true]) ?>
                </div>
                <div class="col-5 pt-4">
                    <p class="form-control mt-2" id="from-person-name"><?= $model->isNewRecord ? '&nbsp;' : $model->fromPerson->dispname ?></p>
                </div>
                <div class="col-3 pt-4">
                    <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-from-person']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <?= $form->field($model, 'to_person_id')->textInput(['id' => 'to-person-id', 'readonly' => true]) ?>
                </div>
                <div class="col-5 pt-4">
                    <p class="form-control mt-2" id="to-person-name"><?= $model->isNewRecord ? '&nbsp;' : $model->toPerson->dispname ?></p>
                </div>
                <div class="col-3 pt-4">
                    <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-to-person']) ?>
                </div>
            </div>
            <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?php if ($model->isNewRecord): ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '登録'), ['class' => 'btn btn-success']) ?>
                <?php else: ?>
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?php endif ?>
                <?= Html::a(Icon::getIconAndLabel('cancel'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
<?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
<?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
<?= $this->render('/person/_select_modal.php', [
        'personIdInput' => 'person-id',
        'personNameInput' => 'person-name',
]);
?>

<?php $this->registerJs("
var mode = 0;
$('#btn-from-person').on('click', function(event){
  mode = 0;
  openPersonSelectModal();
  event.preventDefault();
});
$('#btn-to-person').on('click', function(event){
  mode = 1;
  openPersonSelectModal();
  event.preventDefault();
});
$('#person-id').on('change', function() {
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