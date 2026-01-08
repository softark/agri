<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */
?>

<?php if ($model->person_id) : ?>
    <?php $pw_id = $model->id; ?>
    <p>
        <?= Html::button(Icon::getIcon('link') . ' 名簿へのリンクを変更', [
                'class' => 'btn btn-primary',
                'id' => 'btn-person-select',
        ]) ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
        <?= Html::button(Icon::getIcon('unlink') . ' 名簿へのリンクを解除', [
                'class' => 'btn btn-danger',
                'id' => 'btn-person-unlink',
        ]) ?>
    </p>
    <h3>名簿</h3>
    <?= DetailView::widget([
            'model' => $model->person,
            'attributes' => [
                    [
                            'attribute' => 'type',
                            'value' => function ($model) {
                                return $model->typeText;
                            }
                    ],
                    'dispname',
                    'yomigana',
                    'note',
                    [
                            'label' => '操作',
                            'format' => 'raw',
                            'value' => function ($model) use ($pw_id) {
                                return Html::a(Icon::getIconAndLabel('update'),
                                                ['update-person', 'id' => $pw_id, 'person_id' => $model->id],
                                                ['class' => 'btn btn-primary btn-sm']) . ' ' .
                                        Html::button(Icon::getIconAndLabel('delete'),
                                                ['class' => 'btn btn-danger btn-sm delete-person',
                                                        'data-person-id' => $model->id]);

                            }
                    ]
            ],
    ]) ?>
    <?php $contact_count = count($model->person->contacts); ?>
    <?php if ($contact_count > 0): ?>
        <?php foreach ($model->person->contacts as $contact): ?>
            <h4>連絡先<?= ($contact->order > 1) ? " #" . $contact->order : '' ?></h4>
            <?= DetailView::widget([
                    'model' => $contact,
                    'attributes' => [
                            'fullname',
                            'fulladdress',
                            'phones',
                            'mail',
                            'note',
                            [
                                    'label' => '操作',
                                    'format' => 'raw',
                                    'value' => function ($model) use ($contact_count, $pw_id) {
                                        $buttons = [
                                                Html::a(Icon::getIconAndLabel('update'),
                                                        ['update-contact', 'id' => $pw_id, 'contact_id' => $model->id],
                                                        ['class' => 'btn btn-primary btn-sm']),
                                                Html::button(Icon::getIconAndLabel('delete'),
                                                        ['class' => 'btn btn-danger btn-sm delete-contact',
                                                                'data-contact-id' => $model->id]),
                                                ' &nbsp;&nbsp;&nbsp; 優先順位 :'
                                        ];
                                        if ($model->order > 1) {
                                            $buttons[] = Html::button(Icon::getIconAndLabel('up'),
                                                    ['class' => 'btn btn-primary btn-sm reorder-contact',
                                                            'data-contact-id' => $model->id, 'data-direction' => 'up']);
                                        } else {
                                            $buttons[] = Html::button(Icon::getIconAndLabel('up'),
                                                    ['class' => 'btn btn-secondary btn-sm disabled']);
                                        }
                                        if ($model->order < $contact_count) {
                                            $buttons[] = Html::button(Icon::getIconAndLabel('down'),
                                                    ['class' => 'btn btn-primary btn-sm reorder-contact',
                                                            'data-contact-id' => $model->id, 'data-direction' => 'down']);
                                        } else {
                                            $buttons[] = Html::button(Icon::getIconAndLabel('down'),
                                                    ['class' => 'btn btn-secondary btn-sm disabled']);
                                        }
                                        return implode(' ', $buttons);
                                    }
                            ]
                    ],
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
<?php else : ?>
    <p>
        <?= Html::a(Icon::getIcon('plus-s') . ' 名簿を新規登録',
                ['register', 'id' => $model->id, 'route' => ['view', 'id' => $model->id]],
                ['class' => 'btn btn-success']) ?>
        <?= Html::button(Icon::getIcon('link') . ' 名簿へのリンクを選択',
                ['class' => 'btn btn-primary', 'id' => 'btn-person-select']) ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::hiddenInput('person_name', '', ['id' => 'person-name']) ?>
    </p>
    <h3>名簿</h3>
    <p>名簿なし</p>
<?php endif; ?>

