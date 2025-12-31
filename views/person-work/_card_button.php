<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<td class="col-card-button">
    <?php if ($model->person_id !== null): ?>
        <?= Html::a($model->person->dispname, ['/person/view', 'id' => $model->person_id], ['class' => 'btn btn-primary btn-sm']) ?>
    <?php else: ?>
        <?= Html::a('住所カード登録', ['/person/create', 'work_id' => $model->id], ['class' => 'btn btn-success btn-sm']); ?>
    <?php endif; ?>
</td>
