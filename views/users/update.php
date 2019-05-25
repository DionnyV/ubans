<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\UserForm */

$this->title = Yii::t('app', 'Update: {modelClass}', [
    'modelClass' => $model->user->username,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
