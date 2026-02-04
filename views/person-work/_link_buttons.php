<?php

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<td class="col-link-buttons">
    <?php if ($model->person_id !== null): ?>
        <button type="button" class="btn btn-primary btn-sm add-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link" title="リンク"></i> 変更</button>
        <button type="button" class="btn btn-sm btn-danger del-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link-slash" title="リンク"></i> 解除</button>
    <?php else: ?>
        <button type="button" class="btn btn-success btn-sm add-link" data-model-id="<?= $model->id ?>">
            <i class="fa-solid fa-link" title="リンク"></i> 選択</button>
    <?php endif; ?>
</td>
