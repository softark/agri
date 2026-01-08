<?php

use app\models\Contact;
use app\models\Icon;
use app\models\Person;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\PersonSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = '連絡先';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-index">

    <h1><?= Icon::getIconAndLabel('contact') ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                            'attribute' => 'person_id',
                            'value' => 'person.dispname'
                    ],
                    [
                            'attribute' => 'name',
                            'value' => 'fullname'
                    ],
                    [
                            'attribute' => 'address1',
                            'value' => 'shortaddress'
                    ],
                    [
                            'attribute' => 'phone1',
                            'label' => '電話',
                            'value' => 'phones'
                    ],
                    [
                            'class' => ActionColumn::className(),
                            'urlCreator' => function ($action, Contact $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
