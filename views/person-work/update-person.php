<?php

use app\models\Icon;
use app\models\PersonWork;
use app\models\PersonWorkSearch;
use yii\bootstrap5\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */
/** @var app\models\Person $person */

$this->title = '関係者ワーク : ' . $model->name . ' / 関係者編集';
$this->params['breadcrumbs'][] = ['label' => '関係者ワーク', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '関係者編集';
?>
<div class="person-update">

    <h1><?= Icon::getIcon('update') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_person_form', [
            'model' => $model,
            'person' => $person,
            'route' => ['view', 'id' => $model->id],
    ]) ?>

    <h3>参照している関係者ワーク</h3>
    <?= GridView::widget([
            'dataProvider' => (new PersonWorkSearch(['person_id' => $model->person_id]))->search([]),
            'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                            'attribute' => 'src',
                            'value' => 'srcText',
                    ],
                    'name',
                    'address',
            ]
    ]);
    ?>
</div>
