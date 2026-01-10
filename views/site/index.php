<?php

/** @var yii\web\View $this */

use app\models\Icon;
use yii\bootstrap5\Html;

$this->title = '岩座神農林';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::img('/favicon-96x96.png', ['width' => 56, 'height' => 56, 'style' => 'margin-top:-10px;']) ?>
            岩座神農林</h1>
        <p class="lead">岩座神の農地と山林のデータベース</p>
    </div>

    <div class="body-content text-center">

        <?php if (Yii::$app->user->isGuest): ?>
            <p><?= Html::a(Icon::getIconAndLabel('login'), ['/site/login'], ['class' => "btn btn-lg btn-success"]) ?></p>
        <?php else: ?>
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                    <p><?= Html::a(Icon::getIconAndLabel('person'), ['/person'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                </div>
                <?php if (Yii::$app->user->can('editor')): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                        <p><?= Html::a(Icon::getIconAndLabel('contact'), ['/contact'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                        <p><?= Html::a(Icon::getIconAndLabel('succeed'), ['/person-relation'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (Yii::$app->user->can('admin')): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                        <p><?= Html::a('名簿ワーク', ['/person-work'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                        <p><?= Html::a('棚田', ['/isg-tanada'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                        <p><?= Html::a('山林', ['/isg-forest'], ['class' => 'btn btn-primary btn-lg d-block']) ?></p>
                    </div>
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
</div>
