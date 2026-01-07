<?php

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\widgets\Pjax;

?>

<?php Pjax::begin([
    'formSelector' => '#contact-search-form',
    'id' => 'contact-search-pjax',
    'enablePushState' => false,
    'timeout' => '3000',
]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model) {
        return [
            'class' => 'contact-row',
            'data-contact-id' => $model->id,
            'data-role' => $model->role,
            'data-name1' => $model->name1,
            'data-name2' => $model->name2,
            'data-zip' => $model->zip,
            'data-address1' => $model->address1,
            'data-address2' => $model->address2,
            'data-phone1' => $model->phone1,
            'data-phone2' => $model->phone2,
            'data-mail' => $model->mail,
            'data-note' => $model->note,
            'style' => 'cursor:pointer;',
        ];
    },
    'id' => 'select-contact-list',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'role',
        [
            'attribute' => 'dispname',
        ],
        [
            'attribute' => 'address1',
            'value' => function ($model) {
                return $model->shortAddress;
            }
        ],
        'phones',
    ],
]);?>

<?php Pjax::end();