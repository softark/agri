<?php

use app\components\Icon;
use app\models\PersonWorkSearch;
use yii\bootstrap5\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */
/** @var app\models\Person $person */
/** @var app\models\Contact $contact */

$this->title = '関係者ワーク : ' . $model->name . ' / 連絡先 : ' . $contact->fullname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '関係者ワーク', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '連絡先 : ' . $contact->fullname . ' - 編集';

?>
<div class="contact-create">

    <h1><?= Icon::getIcon('contact') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_contact_form', [
        'model' => $model,
        'person' => $person,
        'contact' => $contact,
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
