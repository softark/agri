<?php

use app\models\Icon;
use app\models\PersonWorkSearch;
use yii\bootstrap5\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var app\models\Contact $contact */

$this->title = '名簿 : ' . $model->dispname . ' / 連絡先 : ' . $contact->fullname . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '名簿', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '連絡先 : ' . $contact->fullname . ' - 編集';

?>
<div class="contact-create">

    <h1><?= Icon::getIcon('contact') . ' ' . Html::encode($this->title) ?></h1>

    <?= $this->render('_contact_form', [
        'model' => $model,
        'contact' => $contact,
    ]) ?>

</div>
