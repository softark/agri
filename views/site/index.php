<?php

/** @var yii\web\View $this */

use app\models\Icon;
use yii\bootstrap5\Html;

$this->title = '岩座神農林業データベース';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::img('/favicon-128x128.png', ['width' => 58, 'height' => 58, 'style' => 'margin-top:-10px;']) ?>
            岩座神農林業データベース</h1>
        <p class="lead">岩座神の農地と山林のデータベース</p>
    </div>

    <div class="body-content text-center">

        <?php if (Yii::$app->user->isGuest): ?>
            <p><?= Html::a(Icon::getIconAndLabel('login'), ['/site/login'], ['class' => "btn btn-lg btn-success"]) ?></p>
        <?php else: ?>
            <div class="row">
            <h2 class="h3">データベース</h2>
            <p>
                <?= Html::a(Icon::getIconAndLabel('field'), ['/field'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a(Icon::getIconAndLabel('tree'), ['/forest'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?= Html::a(Icon::getIconAndLabel('person'), ['/person'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?php if (Yii::$app->user->can('editor')): ?>
                    <?= Html::a(Icon::getIconAndLabel('contact'), ['/contact'], ['class' => 'btn btn-primary btn-lg']) ?>
                    <?= Html::a(Icon::getIconAndLabel('succeed'), ['/person-relation'], ['class' => 'btn btn-primary btn-lg']) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIcon('map-location') . ' i-GIS', 'https://gis.isarigami.net/home', ['class' => 'btn btn-outline-primary btn-lg']) ?>
            </p>
            <?php if (Yii::$app->user->can('editor')): ?>
                </div>
                <div class="row">
                <h2 class="h4">マスター</h2>
                <p>
                    <?= Html::a('字（あざ）', ['/aza'], ['class' => 'btn btn-primary btn-lg']) ?>
                    <?= Html::a('森林タイプ', ['/frtype'], ['class' => 'btn btn-primary btn-lg']) ?>
                    <?= Html::a('農地利用状況', ['/usage'], ['class' => 'btn btn-primary btn-lg']) ?>
                </p>
                <?php if (Yii::$app->user->can('admin')): ?>
                    </div>
                    <div class="row">
                    <h2 class="h4">保守作業</h2>
                    <p>
                        <?= Html::a('関係者ワーク', ['/person-work'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?= Html::a('棚田', ['/isg-tanada'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?= Html::a('山林', ['/isg-forest'], ['class' => 'btn btn-primary btn-lg']) ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            <!--            <p>-->
            <?php //= Html::a(Icon::getIcon('memo') . ' メモを見る', ['/memo/index'], ['class' => "btn btn-lg btn-success"]) ?><!--</p>-->
            <hr/>
            <p><?= Yii::$app->user->identity->getLongName() ?> としてログインしています。</p>
            <p>
                <?= Html::a(Icon::getIconAndLabel('users'), ['/user/index'], ['class' => "btn btn-lg btn-warning"]) ?>
                <?php if (Yii::$app->user->can('admin')): ?>
                    <?= Html::a(Icon::getIconAndLabel('rbac'), ['/rbac'], ['class' => "btn btn-lg btn-warning"]) ?>
                <?php endif; ?>
                <?= Html::a(Icon::getIconAndLabel('logout'), ['/site/logout'], ['class' => "btn btn-lg btn-danger", 'data-method' => 'post']) ?>
            </p>
        <?php endif; ?>

    </div>
