<?php

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

$this->title = '名義';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="person-index">

    <h1><?= Icon::getIconAndLabel('person') ?></h1>

    <p>
        <?php if (Yii::$app->user->can('person.edit')): ?>
            <?= Html::a(Icon::getIconAndLabel('person') . ' に新規登録', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
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
                    [
                            'label' => '連絡先',
                            'value' => function ($model) {
                                if (count($model->contacts) > 0) {
                                    return $model->contacts[0]->fullname;
                                } else {
                                    return '';
                                }
                            }
                    ],
                    [
                            'label' => '住所',
                            'value' => function ($model) {
                                if (count($model->contacts) > 0) {
                                    return $model->contacts[0]->shortAddress;
                                } else {
                                    return '';
                                }
                            }
                    ],
                    [
                            'label' => '電話',
                            'value' => function ($model) {
                                if (count($model->contacts) > 0) {
                                    return $model->contacts[0]->phones;
                                } else {
                                    return '';
                                }
                            }
                    ],
                    [
                            'class' => ActionColumn::class,
                            'template' => (Yii::$app->user->can('person.delete')) ?
                                    '{view} {update} {delete}' :
                                    ((Yii::$app->user->can('person.edit')) ?
                                            '{view} {update}' : '{view}'),
                            'urlCreator' => function ($action, Person $model, $key, $index, $column) {
                                return Url::toRoute([$action, 'id' => $model->id]);
                            }
                    ],
            ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
