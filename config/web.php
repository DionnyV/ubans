<?php

$params = file_exists(__DIR__ . '/params.php') ? require __DIR__ . '/params.php' : null;
$db = file_exists(__DIR__ . '/db.php') ? require __DIR__ . '/db.php' : null;

$config = [
    'id' => 'basic',
    'name' => $params['name'] ?? 'ubans.ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => $params['cookieValidationKey'] ?? 'willBeChanged',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'formatter' => [
            'locale' => 'RU-ru'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'wadeshuler\sendgrid\Mailer',
            'viewPath' => '@app/mail',
            'useFileTransport' => false,
            'apiKey' => $params['apiKey'] ?? null
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                '*' => [
                    'sourceLanguage' => 'ru-RU',
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\PhpManager',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
    'params' => [
        'user.passwordResetTokenExpire' => 3600,
        'bsDependencyEnabled' => false,
        'bsVersion' => 4,
        'adminEmail' => $params['adminEmail'] ?? 'admin@ubans.ru',
        'supportEmail' => $params['supportEmail'] ?? 'no-reply@ubans.ru',
    ],
];

return $config;
