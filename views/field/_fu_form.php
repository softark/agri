<?php

use app\models\Aza;
use app\models\Field;
use app\models\FieldPerson;
use app\models\Icon;
use app\models\Usage;
use kartik\date\DatePicker;
use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\FieldPerson $model */
/** @var app\models\Field $field */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */
?>
<div class="field-person-form">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <?php
            $attributes = [
                // 'id',
                    [
                            'attribute' => 'aza_id',
                            'value' => function ($model) {
                                return $model->aza_name;
                            },
                    ],
                    'p_no',
                    [
                            'attribute' => 'f_area',
                            'value' => function ($model) {
                                return Field::getAreaTextFull($model->f_area);
                            },
                    ],
                    [
                            'attribute' => 'c_area',
                            'value' => function ($model) {
                                return Field::getAreaTextFull($model->c_area);
                            },
                    ],
            ];
            if ($field->note != '') {
                $attributes[] = 'note';
            }
            ?>
            <?= DetailView::widget([
                    'model' => $field,
                    'attributes' => $attributes,
            ]) ?>

            <?php $form = ActiveForm::begin([
                    'id' => 'field-usage-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <h2 class="h4">農地利用状況</h2>
            <table class="table table-bordered table-sm">
                <thead>
                <th>#</th>
                <th>期間</th>
                <th>利用状況</th>
                <th>編集対象</th>
                </thead>
                <tbody>
                <?php if (count($model->field->fieldUsages) == 0) : ?>
                    <tr>
                        <td>1</td>
                        <td>現在</td>
                        <td>未設定</td>
                        <td><?= Icon::getIcon('update') ?></td>
                    </tr>
                <?php else: ?>
                    <?php $i = 1; ?>
                    <?php foreach ($model->field->fieldUsages as $fu): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $fu->valid_from_text ?> ～ <?= $fu->valid_to_text ?></td>
                            <td><?= $fu->usage->name ?></td>
                            <td><?= $fu->id == $model->id ? Icon::getIcon('update') : '&nbsp;' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>

            <?= $form->field($model, 'valid_from')->widget(DatePicker::class,
                    ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']])
                    ->label('FROM ... 不詳な最古の日付は "1900-01-01" を入力') ?>
            <?= $form->field($model, 'usage_id')->dropDownList(Usage::getUsageList()) ?>
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
        <div class="col-lg-8 col-md-6">
            <iframe src="<?= $field->mapurl ?>" style="width:100%; height:75vh;"></iframe>
        </div>
    </div>

</div>
