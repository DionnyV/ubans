<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\UserUpdateForm */

$this->title = Yii::t('app', 'Update: {name}', [
    'name' => $model->userForm->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="users">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_update', [
        'model' => $model,
    ]) ?>
</div>
