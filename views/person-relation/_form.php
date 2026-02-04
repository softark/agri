<?php

use app\components\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

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
                                'options' => ['class' => 'mb-3'],
                                'errorOptions' => ['class' => 'd-none'],
                        ],
                ]); ?>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($model, 'from_person_id')->textInput(['id' => 'from-person-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <?php
                        $fromPersonName = '&nbsp;';
                        if ($model->from_person_id) {
                            $fromPersonName = $model->fromPerson->dispname;
                        }
                        ?>
                        <p class="form-control mt-2" id="from-person-name"><?= $fromPersonName ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-from-person', 'disabled' => !$model->isNewRecord]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($model, 'to_person_id')->textInput(['id' => 'to-person-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <?php
                        $toPersonName = '&nbsp;';
                        if ($model->to_person_id) {
                            $toPersonName = $model->toPerson->dispname;
                        }
                        ?>
                        <p class="form-control mt-2" id="to-person-name"><?= $toPersonName ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-to-person', 'disabled' => !$model->isNewRecord]) ?>
                    </div>
                </div>
                <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>
                <?= $form->errorSummary($model); ?>

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