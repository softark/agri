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
                            'attribute' => 'person.type',
                            'value' => 'person.typetext',
                    ],
                    [
                            'attribute' => 'person.name',
                            'label' => '関係者（氏名／名称）',
                            'format' => 'html',
                            'value' => function ($model) {
                                return Html::a($model->person->dispname, ['/person/view', 'id' => $model->person->id],
                                        ['class' => 'btn btn-sm btn-outline-primary']);
                            },
                    ],
                    'contact_name',
                    [
                            'attribute' => 'address1',
                            'value' => 'shortaddress'
                    ],
                    'phone1',
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
