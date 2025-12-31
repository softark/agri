<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

?>

<?php if ($model->person_id): ?>
    <?= DetailView::widget([
            'model' => $model->person,
            'attributes' => [
                    'dispname',
                    'yomigana',
                    'org_name',
                    'zip',
                    'address',
                    'phone1',
                    'phone2',
                    'memo',
            ],
    ]) ?>
    <p>
        <?= Html::button(Icon::getIcon('link') . ' リンク変更', [
                'class' => 'btn btn-primary',
                'id' => 'btn-person-select',
        ]) ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
        <?= Html::button(Icon::getIconAndLabel('unlink'), [
                'class' => 'btn btn-danger',
                'id' => 'btn-person-unlink',
        ]) ?>
    </p>
<?php else: ?>
    <p>（リンクなし）</p>
    <p>
        <?= Html::button(Icon::getIconAndLabel('link'), [
                'class' => 'btn btn-success',
                'id' => 'btn-person-select',
        ]) ?>
        <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
    </p>
<?php endif; ?>
