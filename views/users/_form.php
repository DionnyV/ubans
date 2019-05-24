<?php

use app\models\form\UserForm;
use app\models\RoleReference;
use app\models\User;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $form yii\widgets\ActiveForm */
/* @var $model UserForm */

$user = $model->user;

?>
<div class="user">
    <?php Pjax::begin(); ?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
    ]); ?>

    <?= $form->errorSummary($user) ?>

    <div class="card-deck mb-3">
        <div class="card">
            <div class="card-header">
                Сайт:
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'password')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'role')->dropdownList(RoleReference::getRoles()) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                Сервер:
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($user, 'nickname')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'steamid')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'flag')->dropdownList($user->getAccountFlags()) ?>
                    <?= $form->field($model, 'options')->checkboxList($user->getAccountOptions()) ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="form-group text-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-secondary']) ?>
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>