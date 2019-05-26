<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Url;

$items = [];
$items[] = ['label' => Yii::t('app', 'Bans'), 'url' => Url::toRoute(['bans/index'])];
$items[] = Yii::$app->user->can('manageUsers')
    ? ['label' => Yii::t('app', 'Users'), 'url' => Url::toRoute(['users/index'])]
    : ['label' => Yii::t('app', 'Account'), 'url' => Url::toRoute(['account/index'])];

$items[] = ['label' => Yii::t('app', 'Servers'), 'url' => Url::toRoute(['servers/index'])];

if (Yii::$app->user->can('manageSettings')) {
    $items[] = ['label' => Yii::t('app', 'Settings'), 'items' => [
        ['label' => Yii::t('app', 'Privileges'), 'url' => Url::toRoute(['privileges/index'])],
    ]];
}

if (Yii::$app->user->isGuest) {
    $items[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
} else {
    $items[] = '<li>'
        . Html::beginForm(['/site/logout'], 'post')
        . Html::submitButton(
            'Выйти (' . Yii::$app->user->identity->username . ')',
            ['class' => 'btn btn-link logout']
        )
        . Html::endForm()
        . '</li>';
}

NavBar::begin([
    'brandLabel' => Yii::$app->name,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => ['class' => 'navbar navbar-expand-lg navbar-light bg-light'],
]);

echo Nav::widget(['items' => $items]);

NavBar::end();
