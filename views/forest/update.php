<?php

use app\components\Icon;
use app\models\Aza;
use app\models\Frtype;
use kartik\date\DatePicker;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\ForestForm $model */
/** @var string|array $ret_route */

$this->title = '山林 : ' . $model->forest->title . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '山林', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->forest->title, 'url' => ['view', 'id' => $model->forest->id]];
$this->params['breadcrumbs'][] = '編集';
?>
    <div class="forest-update">

        <h1><?= Icon::getIconAndLabel('tree') . ' : ' . $model->forest->title . ' - ' . Icon::getIconAndLabel('update') ?></h1>

        <div class="forest-form" id="forest-form">
            <?php $form = ActiveForm::begin([
                    'action' => ['update', 'id' => $model->forest->id, 'ret_route' => $ret_route],
                    'id' => 'forest-edit-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <div class="row">
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">地理情報</h2>
                    <div class="p-2 mb-3 border border-secondary rounded">
                        <?= $form->field($model->forest, 'aza_id')->dropDownList(Aza::getAzaList(), ['prompt' => '']) ?>
                        <?= $form->field($model->forest, 'p_no')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->forest, 'type_id')->dropDownList(Frtype::getTypeList(), ['prompt' => '']) ?>
                        <?= $form->field($model->forest, 'area')->textInput(['disabled' => true])->label('面積 - 単位は ㎡') ?>
                        <?= $form->field($model->forest, 'note')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">所有者</h2>
                    <?php
                    $count = count($model->ofps);
                    ?>
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
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-xl-4 col-lg-6">
                    <h2 class="h5">管理者</h2>
                    <?php
                    $count = count($model->mfps);
                    ?>
                    <?php foreach ($model->mfps as $i => $mfp) : ?>
                        <?php if ($i == $count - 1): ?>
                            <?= $form->field($model, 'new_mfp')
                                    ->checkbox(['class' => 'chk-new-fp-div', 'data-target' => "#div-mfp-$i"]) ?>
                        <?php endif; ?>
                        <?php
                        $div_class = 'p-2 mb-3 border border-secondary rounded';
                        if ($i == $count - 1) {
                            $div_class .= ' collapse';
                            if ($model->new_mfp) {
                                $div_class .= ' show';
                            }
                        }
                        ?>
                        <div class="<?= $div_class ?>" id="div-mfp-<?= $i ?>">
                            <?php if ($i != 0): ?>
                                <?= $form->field($mfp, "[$i]valid_from")->widget(DatePicker::class,
                                        ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']]) ?>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-3">
                                    <?= $form->field($mfp, "[$i]person_id", ['enableError' => false])
                                            ->textInput(['id' => "person-id-mfp-$i", 'readonly' => true]); ?>
                                </div>
                                <div class="col-5 pt-4">
                                    <p class="form-control mt-2"
                                       id="<?= "person-name-mfp-$i" ?>"><?= $mfp->person_id ? $mfp->person->dispname : '&nbsp;'; ?></p>
                                </div>
                                <div class="col-4 pt-4">
                                    <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2 btn-person',
                                            'data-id-field' => "#person-id-mfp-$i", 'data-name-field' => "#person-name-mfp-$i",]) ?>
                                </div>
                            </div>
                            <?= $form->field($mfp, "[$i]note")->textInput(['maxlength' => true]) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?= $form->errorSummary($mfp); ?>
            <div class="form-group">
                <?= Html::button(Icon::getIconAndLabel('map-location'),
                        ['class' => 'btn btn-outline-success', 'id' => 'open-map-modal', 'data-url' => $model->forest->mapurl]) ?>
                <?= Html::submitButton(Icon::getIconAndLabel('ok', '更新'), ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Icon::getIconAndLabel('end-edit'), $ret_route, ['class' => 'btn btn-outline-secondary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <?= $this->render('/field/_map_modal') ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
        <?= $this->render('/person/_select_modal.php', [
                'modalId' => 'person-select-modal',
                'pickerMap' => [
                        'person-id' => '#person-id',
                        'person-name' => '#person-name',
                ],
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
$('#forest-form').on('click', '.btn-person', function(event){
  event.preventDefault();
  person_id_field = $(this).data('id-field');
  person_name_field = $(this).data('name-field');
  $('#person-select-modal').modal('show');
});
$(document).on('picker:selected', '#person-select-modal', function() {
  event.preventDefault();
  $(person_id_field).val($('#person-id').val());
  $(person_name_field).text($('#person-name').val());
});
$('#forest-form').on('change', '.chk-new-fp-div', function(event){
  const target = $(this).data('target');
  if ($(this).prop('checked')) {
    $(target).collapse('show');
  } else {
    $(target).collapse('hide');
  }
});
");
