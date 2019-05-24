<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\UserUpdateForm */

$this->title = Yii::t('app', 'Update: {modelClass}', [
    'modelClass' => $model->userForm->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_update', [
        'model' => $model,
    ]) ?>
</div>
