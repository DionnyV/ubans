<?php

use app\models\form\AccountForm;
use app\models\User;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model AccountForm */

$user = $model->user;
?>

<?php $form = ActiveForm::begin(['id' => 'install-user']); ?>

<div class="card mx-auto" style="max-width: 400px">
    <div class="card-header">Создание пользователя</div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
            <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
            <?= $form->field($user, 'password')->textInput(['maxlength' => true]) ?>

            <p class="text-right">
                <?= Html::submitButton('Далее', ['class' => 'btn btn-dark']) ?>
            </p>
        </li>
    </ul>
</div>

<?php ActiveForm::end(); ?>
