<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\AccountForm*/

$this->title = Yii::t('app', 'Account {name}', [
    'name' => $model->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Account'), 'url' => 'index'];
?>
<div class="account">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
