<?php

use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<td class="col-person-register">
    <?php if ($model->person_id !== null): ?>
        &nbsp;
    <?php else: ?>
        <?= Html::a('登録', ['register', 'id' => $model->id], ['class' => 'btn btn-success btn-sm']); ?>
    <?php endif; ?>
</td>
