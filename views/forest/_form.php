<?php

use app\models\Aza;
use app\models\Frtype;
use app\models\Icon;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Forest $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>

    <div class="forest-form">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <?php $form = ActiveForm::begin([
                        'id' => 'forest-edit-form',
                        'enableAjaxValidation' => false,
                        'options' => ['autocomplete' => 'off'],
                        'fieldConfig' => [
                                'options' => ['class' => 'mb-3']
                        ],
                ]); ?>
                <?= $form->field($model, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
                <?= $form->field($model, 'p_no')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'type_id')->dropDownList(Frtype::getTypeList(), ['prompt' => '']) ?>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($model, 'owner_id')->textInput(['id' => 'owner-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <p class="form-control mt-2" id="owner-name"><?= $model->owner_id ? $model->owner->dispname : '&nbsp;'; ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-owner-person']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <?= $form->field($model, 'manager_id')->textInput(['id' => 'manager-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-5 pt-4">
                        <p class="form-control mt-2" id="manager-name"><?= $model->manager_id ? $model->manager->dispname : '&nbsp;' ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-manager-person']) ?>
                    </div>
                </div>

                <?= $form->field($model, 'area')->textInput(['disabled' => true]) ?>

                <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>


                <div class="form-group">
                    <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                    <?= Html::a(Icon::getIconAndLabel('cancel'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-lg-8 col-md-6">
                <iframe src="<?= $model->mapurl ?>" style="width:100%; height:75vh;"></iframe>
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
$('#btn-owner-person').on('click', function(event){
  mode = 0;
  openPersonSelectModal();
  event.preventDefault();
});
$('#btn-manager-person').on('click', function(event){
  mode = 1;
  openPersonSelectModal();
  event.preventDefault();
});
$('#person-id').on('change', function() {
  event.preventDefault();
  if (mode == 0) {
    $('#owner-id').val($('#person-id').val());
    $('#owner-name').text($('#person-name').val());
  } else {
    $('#manager-id').val($('#person-id').val());
    $('#manager-name').text($('#person-name').val());
  }
});
");