<?php

use app\models\Icon;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */

?>
<?php $person_id = $model->id; ?>
<?php $contact_count = count($model->contacts); ?>
<?php if ($contact_count > 0): ?>
    <?php foreach ($model->contacts as $contact): ?>
        <h4>連絡先<?= ($contact->order > 1) ? " #" . $contact->order : '' ?></h4>
        <?php
        $attributes = [
                'fullname',
                'fulladdress',
                'phones',
                'mail',
                'note',
        ];
        if (Yii::$app->user->can('admin')) {
            $attributes = ArrayHelper::merge($attributes, [
                    [
                            'label' => '登録',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->created_at, 'yyyy-MM-dd HH:mm')
                                        . ' / ' . $model->createdBy->longname;
                            }
                    ],
                    [
                            'label' => '更新',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->updated_at, 'yyyy-MM-dd HH:mm')
                                        . ' / ' . $model->updatedBy->longname;
                            }
                    ],
            ]);
        }
        if (Yii::$app->user->can('contact.edit')) {
            $buttons = [
                    Html::a(Icon::getIconAndLabel('update'),
                            ['update-contact', 'id' => $model->id, 'contact_id' => $contact->id],
                            ['class' => 'btn btn-primary btn-sm']),
            ];
            if (Yii::$app->user->can('contact.delete')) {
                $buttons[] = Html::button(Icon::getIconAndLabel('delete'),
                        ['class' => 'btn btn-danger btn-sm delete-contact',
                                'data-contact-id' => $contact->id]);
            }
            if ($contact_count > 1) {
                $buttons[] = ' &nbsp;&nbsp;&nbsp; 優先順位 :';
                if ($contact->order > 1) {
                    $buttons[] = Html::button(Icon::getIconAndLabel('up'),
                            ['class' => 'btn btn-primary btn-sm reorder-contact',
                                    'data-contact-id' => $contact->id, 'data-direction' => 'up']);
                } else {
                    $buttons[] = Html::button(Icon::getIconAndLabel('up'),
                            ['class' => 'btn btn-secondary btn-sm disabled']);
                }
                if ($contact->order < $contact_count) {
                    $buttons[] = Html::button(Icon::getIconAndLabel('down'),
                            ['class' => 'btn btn-primary btn-sm reorder-contact',
                                    'data-contact-id' => $contact->id, 'data-direction' => 'down']);
                } else {
                    $buttons[] = Html::button(Icon::getIconAndLabel('down'),
                            ['class' => 'btn btn-secondary btn-sm disabled']);
                }
            }
            $attributes[] = [
                    'label' => '操作',
                    'format' => 'raw',
                    'value' => implode(' ', $buttons),
            ];
        }
        ?>
        <?= DetailView::widget([
                'model' => $contact,
                'attributes' => $attributes,
        ]) ?>
    <?php endforeach; ?>
<?php else: ?>
    <h4>連絡先</h4>
    <p>連絡先なし</p>
<?php endif; ?>
<p>
    <?php if (\yii::$app->user->can('contact.create')): ?>
        <?= Html::a(Icon::getIcon('plus-s') . ' 連絡先を追加',
                ['create-contact', 'id' => $model->id],
                ['class' => 'btn btn-success']) ?>
    <?php endif; ?>
</p>
