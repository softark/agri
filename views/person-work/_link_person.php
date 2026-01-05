<?php

use app\models\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<td class="col-link-person">
    <?php if ($model->person_id !== null): ?>
        <?= Html::a($model->person->dispname . ' : ' . $model->person->priorAddress,
                ['/person/view', 'id' => $model->person_id]) ?>
    <?php else: ?>
        &nbsp;
    <?php endif; ?>
</td>
