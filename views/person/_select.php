<?php

/* @var $this yii\web\View */
/* @var $openBtnId string */

/* @var $dataProvider yii\data\ActiveDataProvider */

use app\models\Person;
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
    'rowOptions' => function ($model) {
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
            'attribute' => 'type',
            'value' => 'typeText',
        ],
        [
            'attribute' => 'name',
            'value' => 'dispname'
        ],
        'note',
        [
            'label' => '連絡先',
            'value' => function ($model) {
                if (count($model->contacts) > 0) {
                    return $model->contacts[0]->shortAddress;
                } else {
                    return null;
                }
            },
        ],
        [
            'label' => '電話',
            'value' => function ($model) {
                if (count($model->contacts) > 0) {
                    return $model->contacts[0]->phone1;
                } else {
                    return null;
                }
            },
        ],
    ],
]); ?>
<?php Pjax::end();