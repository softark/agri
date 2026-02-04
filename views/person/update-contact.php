<?php

use app\components\Icon;

/** @var yii\web\View $this */
/** @var app\models\Person $model */
/** @var app\models\Contact $contact */

$contact_label = ($model->contact->contact_name == '') ? '連絡先' : '連絡先 : ' . $model->contact->contact_name;
$this->title = '関係者 : ' . $model->dispname . ' / ' . $contact_label . ' - 編集';
$this->params['breadcrumbs'][] = ['label' => '関係者', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dispname, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $contact_label . ' - 編集';

?>
<div class="contact-create">

    <h1><?= Icon::getIcon('contact') . ' 関係者 : ' . $model->dispname . ' / ' .  $contact_label . ' - ' . Icon::getIconAndLabel('update') ?></h1>

    <?= $this->render('_contact_form', [
        'model' => $model,
        'contact' => $contact,
    ]) ?>

</div>
