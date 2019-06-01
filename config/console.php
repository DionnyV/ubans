<?php

$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\commands',
    'components' => [
        'db' => $db,
    ],
];

return $config;
