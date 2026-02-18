<?php

/** @var yii\web\View $this */

use app\components\Icon;
use yii\bootstrap5\Html;

$this->title = '岩座神農林業データベース';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::img('/favicon-128x128.png', ['width' => 58, 'height' => 58, 'style' => 'margin-top:-10px;']) ?>
            岩座神農林業データベース</h1>
        <p class="lead">岩座神の農地と山林のデータベース</p>
    </div>

    <div class="body-content">
        <div class="row">
            <?php if (Yii::$app->user->isGuest): ?>
                <p class="text-center"><?= Html::a(Icon::getIconAndLabel('login'), ['/site/login'], ['class' => "btn btn-lg btn-success"]) ?></p>
            <?php else: ?>
            <div class="col-md-6">
                <div class="m-2 p-3 border border-2 rounded-2">
                    <h2 class="h3">データベース</h2>
                    <p>
                        <?= Html::a(Icon::getIconAndLabel('field'), ['/field'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?= Html::a(Icon::getIconAndLabel('tree'), ['/forest'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?= Html::a(Icon::getIconAndLabel('person'), ['/person'], ['class' => 'btn btn-primary btn-lg']) ?>
                        <?php if (Yii::$app->user->can('admin')): ?>
                            <?= Html::a(Icon::getIconAndLabel('contact'), ['/contact'], ['class' => 'btn btn-warning btn-lg']) ?>
                            <?= Html::a(Icon::getIconAndLabel('succeed'), ['/person-relation'], ['class' => 'btn btn-warning btn-lg']) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php if (Yii::$app->user->can('editor')): ?>
                <div class="col-md-6">
                    <div class="m-2 p-3 border border-2 rounded-2">
                        <h2 class="h4">マスター</h2>
                        <p>
                            <?php if (Yii::$app->user->can('admin')): ?>
                                <?= Html::a('字（あざ）', ['/aza'], ['class' => 'btn btn-warning btn-lg']) ?>
                                <?= Html::a('森林タイプ', ['/frtype'], ['class' => 'btn btn-warning btn-lg']) ?>
                            <?php endif; ?>
                            <?= Html::a('農地利用状況', ['/usage'], ['class' => 'btn btn-primary btn-lg']) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (Yii::$app->user->can('admin')): ?>
                <div class="col-md-6">
                    <div class="m-2 p-3 border border-2 rounded-2">
                        <h2 class="h4">保守作業</h2>
                        <p>
                            <?= Html::a('関係者ワーク', ['/person-work'], ['class' => 'btn btn-primary btn-lg']) ?>
                            <?= Html::a('棚田', ['/isg-tanada'], ['class' => 'btn btn-primary btn-lg']) ?>
                            <?= Html::a('山林', ['/isg-forest'], ['class' => 'btn btn-primary btn-lg']) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="col-md-6">
                <div class="m-2 p-3 border border-2 rounded-2">
                    <h2 class="h4">地図</h2>
                    <p>
                        <?= Html::a(Html::img('/i-gis.svg', ['style' => 'height:32px']) . ' 岩座神農林業地図', 'https://gis.isarigami.net/?t=isg-agfr', ['class' => 'btn btn-outline-primary btn-lg']) ?>
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="m-2 p-3 border border-2 rounded-2">
                    <p class=""><?= Yii::$app->user->identity->getLongName() ?> としてログインしています。</p>
                    <p>
                        <?= Html::a(Icon::getIconAndLabel('users'), ['/user/index'], ['class' => "btn btn-lg btn-warning"]) ?>
                        <?php if (Yii::$app->user->can('admin')): ?>
                            <?= Html::a(Icon::getIconAndLabel('rbac'), ['/rbac'], ['class' => "btn btn-lg btn-warning"]) ?>
                        <?php endif; ?>
                        <?= Html::a(Icon::getIconAndLabel('logout'), ['/site/logout'], ['class' => "btn btn-lg btn-danger", 'data-method' => 'post']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>