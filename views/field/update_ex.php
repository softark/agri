<?php

use app\models\Aza;
use app\models\Frtype;
use app\models\Icon;
use app\models\Usage;
use kartik\date\DatePicker;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\FieldForm $model */
/** @var string|array $ret_route */

$this->title = '農地 : ' . $model->field->p_no . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '農地', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->field->p_no, 'url' => ['view', 'id' => $model->field->id]];
$this->params['breadcrumbs'][] = '編集';
?>
    <div class="field-update">

        <h1><?= Icon::getIconAndLabel('field') . ' : ' . $model->field->p_no . ' - ' . Icon::getIconAndLabel('update') ?></h1>

        <div class="field-form" id="field-form">
            <div class="row">
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">地理情報</h2>
                    <?php $form = ActiveForm::begin([
                            'action' => ['update', 'mode' => 'f', 'id' => $model->field->id, 'ret_route' => $ret_route],
                            'id' => 'field-edit-form',
                            'enableAjaxValidation' => false,
                            'options' => ['autocomplete' => 'off'],
                            'fieldConfig' => [
                                    'options' => ['class' => 'mb-3']
                            ],
                    ]); ?>
                    <div class="p-2 mb-3 border border-secondary rounded">
                        <?= $form->field($model->field, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
                        <?= $form->field($model->field, 'p_no')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->field, 'c_area')->textInput(['disabled' => true])->label('地図面積 - 単位は ㎡') ?>
                        <?= $form->field($model->field, 'f_area')->textInput()->label('公称面積 - ㎡ で入力') ?>
                        <?= $form->field($model->field, 'note')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="form-group">
                        <?= Html::button(Icon::getIconAndLabel('map-location'),
                                ['class' => 'btn btn-outline-success', 'id' => 'open-map-modal', 'data-url' => $model->field->mapurl]) ?>
                        <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Icon::getIconAndLabel('end-edit'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">所有者</h2>
                    <?php
                    $count = count($model->ofps);
                    ?>
                    <?php $form = ActiveForm::begin([
                            'action' => ['update', 'mode' => 'o', 'id' => $model->field->id, 'ret_route' => $ret_route],
                            'id' => 'field-ofps-edit-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'options' => ['autocomplete' => 'off'],
                            'fieldConfig' => [
                                    'options' => ['class' => 'mb-3'],
                                    'errorOptions' => ['class' => 'd-none'],
                            ],
                    ]); ?>
                    <?php foreach ($model->ofps as $i => $ofp) : ?>
                        <?php if ($i == $count - 1): ?>
                            <?= $form->field($model, 'new_ofp')
                                    ->checkbox(['class' => 'chk-new-fp-div', 'data-target' => "#div-ofp-$i"]) ?>
                        <?php endif; ?>
                        <?php
                        $div_class = 'p-2 mb-3 border border-secondary rounded';
                        if ($i == $count - 1) {
                            $div_class .= ' collapse';
                            if ($model->new_ofp) {
                                $div_class .= ' show';
                            }
                        }
                        ?>
                        <div class="<?= $div_class ?>" id="div-ofp-<?= $i ?>">
                            <?php if ($i != 0): ?>
                                <?= $form->field($ofp, "[$i]valid_from")->widget(DatePicker::class,
                                        ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']]) ?>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-3">
                                    <?= $form->field($ofp, "[$i]person_id", ['enableError' => false])
                                            ->textInput(['id' => "person-id-ofp-$i", 'readonly' => true]); ?>
                                </div>
                                <div class="col-5 pt-4">
                                    <p class="form-control mt-2"
                                       id="<?= "person-name-ofp-$i" ?>"><?= $ofp->person_id ? $ofp->person->dispname : '&nbsp;'; ?></p>
                                </div>
                                <div class="col-4 pt-4">
                                    <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2 btn-person',
                                            'data-id-field' => "#person-id-ofp-$i", 'data-name-field' => "#person-name-ofp-$i",]) ?>
                                </div>
                            </div>
                            <?= $form->field($ofp, "[$i]note")->textInput(['maxlength' => true]) ?>
                            <?= $form->errorSummary($ofp); ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Icon::getIconAndLabel('end-edit'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">耕作者</h2>
                    <?php
                    $count = count($model->cfps);
                    ?>
                    <?php $form = ActiveForm::begin([
                            'action' => ['update', 'mode' => 'c', 'id' => $model->field->id, 'ret_route' => $ret_route],
                            'id' => 'field-cfps-edit-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'options' => ['autocomplete' => 'off'],
                            'fieldConfig' => [
                                    'options' => ['class' => 'mb-3'],
                                    'errorOptions' => ['class' => 'd-none'],
                            ],
                    ]); ?>
                    <?php foreach ($model->cfps as $i => $cfp) : ?>
                        <?php if ($i == $count - 1): ?>
                            <?= $form->field($model, 'new_cfp')
                                    ->checkbox(['class' => 'chk-new-fp-div', 'data-target' => "#div-cfp-$i"]) ?>
                        <?php endif; ?>
                        <?php
                        $div_class = 'p-2 mb-3 border border-secondary rounded';
                        if ($i == $count - 1) {
                            $div_class .= ' collapse';
                            if ($model->new_cfp) {
                                $div_class .= ' show';
                            }
                        }
                        ?>
                        <div class="<?= $div_class ?>" id="div-cfp-<?= $i ?>">
                            <?php if ($i != 0): ?>
                                <?= $form->field($cfp, "[$i]valid_from")->widget(DatePicker::class,
                                        ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']]) ?>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-3">
                                    <?= $form->field($cfp, "[$i]person_id", ['enableError' => false])
                                            ->textInput(['id' => "person-id-cfp-$i", 'readonly' => true]); ?>
                                </div>
                                <div class="col-5 pt-4">
                                    <p class="form-control mt-2"
                                       id="<?= "person-name-cfp-$i" ?>"><?= $cfp->person_id ? $cfp->person->dispname : '&nbsp;'; ?></p>
                                </div>
                                <div class="col-4 pt-4">
                                    <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2 btn-person',
                                            'data-id-field' => "#person-id-cfp-$i", 'data-name-field' => "#person-name-cfp-$i",]) ?>
                                </div>
                            </div>
                            <?= $form->field($cfp, "[$i]note")->textInput(['maxlength' => true]) ?>
                            <?= $form->errorSummary($cfp); ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Icon::getIconAndLabel('end-edit'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">利用状況</h2>
                    <?php
                    $count = count($model->fus);
                    ?>
                    <?php $form = ActiveForm::begin([
                            'action' => ['update', 'mode' => 'u', 'id' => $model->field->id, 'ret_route' => $ret_route],
                            'id' => 'field-fus-edit-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'options' => ['autocomplete' => 'off'],
                            'fieldConfig' => [
                                    'options' => ['class' => 'mb-3'],
                                    'errorOptions' => ['class' => 'd-none'],
                            ],
                    ]); ?>
                    <?php foreach ($model->fus as $i => $fu) : ?>
                        <?php if ($i == $count - 1): ?>
                            <?= $form->field($model, 'new_fu')
                                    ->checkbox(['class' => 'chk-new-fp-div', 'data-target' => "#div-fu-$i"]) ?>
                        <?php endif; ?>
                        <?php
                        $div_class = 'p-2 mb-3 border border-secondary rounded';
                        if ($i == $count - 1) {
                            $div_class .= ' collapse';
                            if ($model->new_fu) {
                                $div_class .= ' show';
                            }
                        }
                        ?>
                        <div class="<?= $div_class ?>" id="div-uf-<?= $i ?>">
                            <?php if ($i != 0): ?>
                                <?= $form->field($fu, "[$i]valid_from")->widget(DatePicker::class,
                                        ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']]) ?>
                            <?php endif; ?>
                            <?= $form->field($fu, "[$i]usage_id")->dropDownList(Usage::getUsageList()) ?>
                            <?= $form->field($cfp, "[$i]note")->textInput(['maxlength' => true]) ?>
                            <?= $form->errorSummary($cfp); ?>
                        </div>
                    <?php endforeach; ?>
                    <div class="form-group">
                        <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                        <?= Html::a(Icon::getIconAndLabel('end-edit'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <?= $this->render('/field/_map_modal') ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
        <?= $this->render('/person/_select_modal.php', [
                'personIdInput' => 'person-id',
                'personNameInput' => 'person-name',
        ]); ?>
    </div>

<?php
$this->registerJs("
$('#open-map-modal').on('click', function(e) {
  e.preventDefault();
  const src = $(this).data('url');
  openMapModal(src);
});
var person_id_field = '';
var person_name_field = '';
$('#field-form').on('click', '.btn-person', function(event){
  event.preventDefault();
  person_id_field = $(this).data('id-field');
  person_name_field = $(this).data('name-field');
  openPersonSelectModal();
});
$('#person-id').on('change', function() {
  event.preventDefault();
  $(person_id_field).val($('#person-id').val());
  $(person_name_field).text($('#person-name').val());
});
$('#field-form').on('change', '.chk-new-fp-div', function(event){
  const target = $(this).data('target');
  if ($(this).prop('checked')) {
    $(target).collapse('show');
  } else {
    $(target).collapse('hide');
  }
});
");
