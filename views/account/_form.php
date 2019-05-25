<?php

use app\models\form\AccountForm;
use app\models\User;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model AccountForm */

$user = $model->user;
?>
<div class="account">
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
                    <?= $form->field($user, 'email')->textInput(['maxlength' => true, 'disabled' => true]) ?>

                    <label for="unique-url">Ваша уникальная ссылка</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="auth_key"
                               onclick="this.select()"
                               data-url="<?= Yii::$app->request->hostInfo . Url::to(['site/auth', 'code' => '']) ?>"
                               value="<?= Yii::$app->request->hostInfo . Url::to(['site/auth', 'code' => $user->auth_key]) ?>"
                               readonly>
                        <div class="input-group-append">
                            <span class="input-group-text" onclick="Account.generateAuthKey()">#</span>
                        </div>
                    </div>
                    <?= $form->field($user, 'auth_key')->hiddenInput()->label(false); ?>
                    <?= $form->field($user, 'nickname')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'steamid')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'password')->textInput(['maxlength' => true]) ?>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-header">Привилегии:</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($model->privileges as $privilege) : ?>
                    <li class="list-group-item">
                        <h5 class="card-title"> <?= $privilege->server->hostname ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted"> <?= $privilege->server->address ?></h6>
                        <?php foreach ($privilege->server->privileges as $item) :
                            if ($item->access_flags === $privilege->access_flags) : ?>
                                <strong><?= $item->name; ?></strong> до
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <mark><?= date('d.m.Y H:i', $privilege->expire) ?></mark>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="form-group text-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['site/index'], ['class' => 'btn btn-light']) ?>
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
<script>
    var Account = {
        generateAuthKey: function () {
            var randomKey = Math.random().toString(36).substr(2) + '_' + Math.random().toString(36).substr(2);
            var prepentUrl =  $("input[name='auth_key'").attr("data-url");
            $("input[name='auth_key'").val(prepentUrl + randomKey);
            $("input[name='User[auth_key]'").val(randomKey);
        }
    }
</script>