<?php

/** @var yii\web\View $this */

use app\models\Icon;
use yii\bootstrap5\Html;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4"><?= Html::img('/favicon-96x96.png', ['width' => 56, 'height' => 56, 'style' => 'margin-top:-10px;']) ?>
            岩座神農会</h1>
        <p class="lead">岩座神の農地と山林のデータベース</p>
    </div>

    <div class="body-content text-center">

        <?php if (Yii::$app->user->isGuest): ?>
            <p><?= Html::a(Icon::getIconAndLabel('login'), ['/site/login'], ['class' => "btn btn-lg btn-success"]) ?></p>
        <?php else: ?>
            <p><?= Html::a(Icon::getIcon('memo') . ' メモを見る', ['/memo/index'], ['class' => "btn btn-lg btn-success"]) ?></p>
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
