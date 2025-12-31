<?php

use app\models\Icon;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\PersonWork $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '名簿ワーク', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div id="person-work-view" class="person-work-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-6 col-md-8">

            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                            'id',
                            [
                                    'attribute' => 'src',
                                    'value' => function ($model) {
                                        return $model->srcText;
                                    },
                            ],
                            'name',
                            'address',
                    ],
            ]) ?>
            <p>
                <?= Html::a(Icon::getIconAndLabel('delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                                'confirm' => '名簿ワークエントリ <strong>"' . $model->name . '"</strong> を削除しますか？',
                                'method' => 'post',
                        ],
                ]) ?>
                <?= Html::a(Icon::getIconAndLabel('go-back'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            </p>

            <h3>名簿へのリンク</h3>
            <div id="person-link">
                <?php if ($model->person_id) : ?>
                    <?= DetailView::widget([
                            'model' => $model->person,
                            'attributes' => [
                                    'dispname',
                                    'yomigana',
                                    'typeText',
                                    'note',
                            ],
                    ]) ?>
                <?php else : ?>
                    リンクなし
                <?php endif; ?>
            </div>
            <h3>連絡先へのリンク</h3>
            <div id="contact-link">
                <?php if ($model->contact_id) : ?>
                    <?= DetailView::widget([
                            'model' => $model->contact,
                            'attributes' => [
                                    'zip',
                                    'address',
                                    'phone1',
                                    'phone2',
                                    'mail',
                                    'note',
                            ],
                    ]) ?>
                <?php else : ?>
                    リンクなし
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

