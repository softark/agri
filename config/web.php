<?php

Yii::setAlias('@common', dirname(__DIR__) . '/common');

$config = [
    'id' => 'agri',
    'name' => '岩座神農会',
    'language' => 'ja-JP',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'session'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'mqzVmDBdfzNs-DEgRoBZdNJ1kXwZvA8H',
        ],
        'cache' => [
            'class' => yii\caching\ApcCache::class,
            'useApcu' => true,
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'session' => [
            'class' => yii\web\DbSession::class,
            'timeout' => '600',
            'cookieParams' => [
                'httpOnly' => true,
                'secure' => true,
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'authManager' => [
            'class' => yii\rbac\DbManager::class,
            'defaultRoles' => ['guest', 'friend'],
            'cache' => 'cache',
            'cacheKey' => 'agri-rbac',
        ],
    ],
    'modules' => [
        'rbac' => [
            'class' => mdm\admin\Module::class,
            'controllerMap' => [
                'assignment' => [
                    'class' => mdm\admin\controllers\AssignmentController::class,
                    'searchClass' => \common\auth\UserSearch::class,
                    'usernameField' => 'username',
                    'fullnameField' => 'dispname',
                    'extraColumns' => [
                        'dispname',
                        [
                            'attribute' => 'roleText',
                            'format' => 'ntext',
//                            'contentOptions' => ['class' => 'col-sm-3'],
                        ],
                    ],
                ]
            ],
            'layout' => 'left-menu',
            'mainLayout' => '@app/views/layouts/main.php',
        ],
    ],
    'as access' => [
        'class' => mdm\admin\components\AccessControl::class,
        'allowActions' => [
            'site/*',
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
];
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['192.168.0.*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['192.168.0.*'],
    ];
    $config['as access']['allowActions'][] = 'gii/*';
    $config['as access']['allowActions'][] = 'debug/*';
}

return $config;
