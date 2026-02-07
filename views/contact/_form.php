<?php

use app\components\Icon;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Contact $model */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var string|array $ret_route */

?>

<div class="contact-form" id="contact-form">
    <div class="row">
        <div class="col-lg-5">
            <?php
            $attributes = [
                    [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return $model->typeText;
                            },
                    ],
                    'dispname',
                    'yomigana',
            ];
            if ($model->person->note != '') $attributes[] = 'note';
            ?>
            <?= DetailView::widget([
                    'model' => $model->person,
                    'attributes' => $attributes
            ]) ?>
            <?php $form = ActiveForm::begin([
                    'id' => 'contact-edit-form',
                    'enableAjaxValidation' => false,
                    'options' => ['autocomplete' => 'off'],
                    'fieldConfig' => [
                            'options' => ['class' => 'mb-3']
                    ],
            ]); ?>
            <?= $this->render('_contact_sub.php', ['form' => $form, 'model' => $model]) ?>
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
        <?php if (count($model->person->contacts) > 1): ?>
            <p>※ この関係者には複数の連絡先があります。連絡先の優先順位の変更は、関係者の閲覧画面で行うことが出来ます。
                <?= Html::a(Icon::getIcon('view') . ' ' . $model->person->dispname,
                        ['/person/view', 'id' => $model->person->id],
                        ['class' => 'btn btn-outline-primary']) ?>
            </p>
        <?php endif; ?>
    </div>
    <?= $this->render('/contact/_select_modal.php', [
            'modalId' => 'contact-select-modal',
            'pickerMap' => [
                    'role' => '#role',
                    'name1' => '#contact-name1',
                    'name2' => '#contact-name2',
                    'zip' => '#zip',
                    'address1' => '#address1',
                    'address2' => '#address2',
                    'phone1' => '#phone1',
                    'phone2' => '#phone2',
                    'mail' => '#mail',
                    'note' => '#contact-note',
            ],
    ]); ?>
</div>
