<?php

use app\models\Field;
use app\models\FieldPerson;
use app\models\Icon;
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
                        'id' => 'field-person-edit-form',
                        'enableAjaxValidation' => false,
                        'options' => ['autocomplete' => 'off'],
                        'fieldConfig' => [
                                'options' => ['class' => 'mb-3']
                        ],
                ]); ?>
                <?php $role_text = $model->role == FieldPerson::ROLE_OWNER ? '所有者' : '耕作者'; ?>
                <h2 class="h4"><?= $role_text ?></h2>
                <table class="table table-bordered table-sm">
                    <thead>
                    <th>#</th>
                    <th>期間</th>
                    <th><?= $role_text ?></th>
                    <th>編集対象</th>
                    </thead>
                    <tbody>
                    <?php if ($model->role == FieldPerson::ROLE_OWNER): ?>
                        <?php if (count($model->field->ownerFieldPersons) == 0) : ?>
                            <tr>
                                <td>1</td>
                                <td>現在</td>
                                <td>未設定</td>
                                <td><?= Icon::getIcon('update') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; ?>
                            <?php foreach ($model->field->ownerFieldPersons as $ownerFp): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $ownerFp->valid_from_text ?> ～ <?= $ownerFp->valid_to_text ?></td>
                                    <td><?= $ownerFp->person->name ?></td>
                                    <td><?= $ownerFp->id == $model->id ? Icon::getIcon('update') : '&nbsp;' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if (count($model->field->cultivatorFieldPersons) == 0) : ?>
                            <tr>
                                <td>1</td>
                                <td>現在</td>
                                <td>未設定</td>
                                <td><?= Icon::getIcon('update') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; ?>
                            <?php foreach ($model->field->cultivatorFieldPersons as $cultivatorFp): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $cultivatorFp->valid_from_text ?> ～ <?= $cultivatorFp->valid_to_text ?></td>
                                    <td><?= $cultivatorFp->person->name ?></td>
                                    <td><?= $cultivatorFp->id == $model->id ? Icon::getIcon('update') : '&nbsp;' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    </tbody>
                </table>

                <?= $form->field($model, 'valid_from')->widget(DatePicker::class,
                        ['type' => DatePicker::TYPE_COMPONENT_APPEND, 'pluginOptions' => ['format' => 'yyyy-mm-dd']])
                        ->label('FROM ... 不詳な最古の日付は "1900-01-01" を入力') ?>
                <div class="row">
                    <div class="col-3">
                        <?= $form->field($model, 'person_id')
                                ->textInput(['id' => 'person-id', 'readonly' => true]) ?>
                    </div>
                    <div class="col-6 pt-4">
                        <p class="form-control mt-2"
                           id="person-name" disabled><?= $model->person_id ? $model->person->dispname : '&nbsp;'; ?></p>
                    </div>
                    <div class="col-3 pt-4">
                        <?= Html::button('選択 ...', ['class' => 'btn btn-primary mt-2', 'id' => 'btn-person']) ?>
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
            <div class="col-lg-8 col-md-6">
                <iframe src="<?= $field->mapurl ?>" style="width:100%; height:75vh;"></iframe>
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
$('#btn-person').on('click', function(event){
  openPersonSelectModal();
  event.preventDefault();
});
$('#person-id').on('change', function() {
  event.preventDefault();
  $('#person-id').val($('#person-id').val());
  $('#person-name').text($('#person-name').val());
});
");