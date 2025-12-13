<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\assets\BootboxAsset;
use app\assets\FontAwesomeAsset;
use app\widgets\Alert;
use app\models\Icon;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
BootboxAsset::overrideSystemConfirm();
FontAwesomeAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="農会" />
    <link rel="manifest" href="/site.webmanifest" />
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('/favicon-96x96.png', ['width' => 24, 'height' => 24, 'style' => 'margin-top:-8px;']) . ' ' . Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
    ]);
    $items = [
    ];
    if (!Yii::$app->user->isGuest) {
//        $items[] = ['label' => Icon::getIconAndLabel('memo'), 'url' => ['/memo'], 'encode' => false];
        if (Yii::$app->user->can('admin')) {
            $items[] = ['label' => '棚田', 'url' => ['/tanada'], 'encode' => false];
        }
    }
    echo Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto'],
            'items' => $items,
            'activateParents' => true,
            'encodeLabels' => false,
    ]);
    $items = [
        // ['label' => 'HOME', 'url' => ['/site/index'], 'encode' => false],
    ];
    if (Yii::$app->user->isGuest) {
        $items[] = ['label' => Icon::getIconAndLabel('login'), 'url' => ['/site/login'], 'encode' => false];
    } else {
        $items[] = ['label' => Icon::getIconAndLabel('users'), 'url' => ['/user/index'], 'encode' => false];
        if (Yii::$app->user->can('admin')) {
            $items[] = ['label' => Icon::getIconAndLabel('rbac'), 'url' => ['/rbac'], 'encode' => false];
        }
        $items[] = ['label' => Icon::getIcon('user') . ' ' . Yii::$app->user->identity->longname, 'url' => ['/user/view', 'id' => Yii::$app->user->identity->id], 'encode' => false];
        $items[] = ['label' => Icon::getIconAndLabel('logout'), 'url' => ['/site/logout'], 'encode' => false, 'linkOptions' => ['data-method' => 'post']];
    }
    echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'encodeLabels' => false,
            'items' => $items,
    ]);
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 bg-light">
    <div class="container">
        <div class="row text-muted">
            <div class="col-md-6 text-center text-md-start">&copy; isarigami.net <?= date('Y') ?></div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
