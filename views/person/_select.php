<?php

/* @var $this yii\web\View */
/* @var $openBtnId string */

/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\grid\GridView;
use yii\widgets\Pjax;
?>

<?php Pjax::begin([
    'formSelector' => '#person-search-form',
    'id' => 'person-search-pjax',
    'enablePushState' => false,
    'timeout' => '3000',
]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function($model) {
        return [
            'class' => 'person-row',
            'data-person-id' => $model->id,
            'style' => 'cursor:pointer;',
        ];
    },
    'id' => 'select-person-list',
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'value' => 'dispname'
        ],
        [
            'attribute' => 'address',
            'value' => 'shortaddress'
        ],
        'phone1',
        'phone2',
        'memo',
    ],
]); ?>
<?php Pjax::end();