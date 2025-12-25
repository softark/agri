<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '住所録辞書', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="person-work-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'id',
                            'name',
                            'address',
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                                'confirm' => '住所録辞書エントリ <strong>"' . $model->name . '"</strong> を削除しますか？',
                                'method' => 'post',
                        ],
                ]) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>

            <h3>住所カードへのリンク</h3>
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
                    <?= Html::button(Icon::getIcon('link') . ' リンク変更', ['class' => 'btn btn-primary', 'id' => 'btn-person-select']) ?>
                    <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
                    <?= Html::a(Icon::getIconAndLabel('unlink'), ['delete-link', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                    'confirm' => '住所カードへのリンクを削除しますか？',
                                    'method' => 'post',
                            ],
                    ]) ?>
                </p>
            <?php else: ?>
                <p>（リンクなし）</p>
                <p>
                    <?= Html::button(Icon::getIconAndLabel('link'), ['class' => 'btn btn-success', 'id' => 'btn-person-select']) ?>
                    <?= Html::hiddenInput('person_id', '', ['id' => 'person-id']) ?>
                </p>
            <?php endif; ?>
            <?php
            echo $this->render('/person/_select_modal.php', [
                    'personIdInput' => 'person-id',
            ]);
            ?>

        </div>
    </div>
</div>

<?php
$url = Url::to(['/person-work/add-link']);
$this->registerJs("
$('#btn-person-select').on('click', function(event){
    openPersonSelectModal();
    event.preventDefault();
});
$('#person-id').on('change', function() {
  $.post('$url' + '?id=' + {$model->id} + '&person_id=' + $(this).val());
});
");
