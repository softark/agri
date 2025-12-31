<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<td class="col-link-buttons">
    <?php if ($model->person_id !== null): ?>
        <button type="button" class="btn btn-primary btn-sm add-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link" title="リンク変更"></i> リンク変更</button>
        <button type="button" class="btn btn-sm btn-danger del-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link-slash" title="リンク解除"></i> リンク解除</button>
    <?php else: ?>
        <button type="button" class="btn btn-success btn-sm add-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link" title="リンク選択"></i> リンク選択</button>
    <?php endif; ?>
</td>
