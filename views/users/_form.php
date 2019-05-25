<?php

use app\models\form\UserForm;
use app\models\RoleReference;
use app\models\User;
use app\services\ServerService;
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $user User */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model UserForm */

$user = $model->user;
?>
<div class="user">
    <?php Pjax::begin(); ?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->errorSummary($model) ?>

    <div class="card-deck mb-3">
        <div class="card">
            <div class="card-header">Сайт:</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($user, 'email')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'password')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'auth_key', ['template' => '
                           {label}
                           <div class="input-group">
                              {input}
                              <div class="input-group-append">
                                <span class="input-group-text" onclick="UserForm.generateAuthKey()">#</span>
                              </div>
                               {error}{hint}
                           </div>
                           <small class="form-text text-muted"></small>
                       '])->textInput(['maxlength' => true]); ?>
                    <?= $form->field($model, 'role')->dropdownList(RoleReference::getRoles()) ?>
                    <?= $form->field($user, 'status')->dropdownList(User::getStatusLabels()) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Сервер:</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($user, 'nickname')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($user, 'steamid')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'flag')->dropdownList($user->getAccountFlags()) ?>
                    <?= $form->field($model, 'options')->checkboxList($user->getAccountOptions()) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">Привилегии:</div>
            <div class="card-body">
                <?php foreach ($model->privileges as $privilege) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               id="server-<?= $privilege->server_id ?>"
                            <?= empty($privilege->access_flags) ? '' : 'checked' ?>
                            <?= $privilege->isNewRecord ? '' :
                                'data-id="' . $privilege->user_id . '-' . $privilege->server_id . '"' ?>
                               onchange="UserForm.toggleAccess(this)">
                        <label class="form-check-label" for="server-<?= $privilege->server_id ?>">
                            <?= $privilege->server->hostname ?>
                        </label>
                    </div>
                    <span id="privilege-<?= $privilege->server_id ?>"
                          style="display:<?= !empty($privilege->access_flags) ? 'block' : 'none' ?>;">
                        <?= $form->field($privilege, "[$privilege->server_id]access_flags")
                            ->dropdownList(
                                ServerService::getPrivilegesList($privilege->server),
                                ['disabled' => empty($privilege->access_flags)]
                            ) ?>

                        <?= $form->field($privilege, "[$privilege->server_id]expire")
                            ->widget(DateTimePicker::class, [
                                'type' => DateTimePicker::TYPE_INPUT,
                                'options' => [
                                    'placeholder' => 'Ввод даты/времени...',
                                    'value' => Yii::$app->formatter->asDatetime($privilege->expire ?? time(), 'dd.MM.Y hh:mm'),
                                    'disabled' => empty($privilege->access_flags),
                                ],
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd.MM.yyyy hh:i',
                                    'startDate' => Yii::$app->formatter->asDatetime(time(), 'dd.MM.Y hh:mm'),
                                    'autoclose' => true,
                                    'weekStart' => 1,
                                ]
                            ]);
                        ?>
                        <hr>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <div class="form-group text-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-light']) ?>
        <?php if (!$user->isNewRecord) : ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $user->id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
<script>
    var UserForm = {
        generateAuthKey: function () {
            var randomKey = Math.random().toString(36).substr(2) + '_' + Math.random().toString(36).substr(2);

            $("input[name='User[auth_key]'").val(randomKey);
        },
        toggleAccess: function (el) {
            var $el = $(el);
            var id = $el.attr("id").split("-")[1];
            var $inputBlock = $("#privilege-" + id);
            var $accessFlags = $("#access-" + id + "-access_flags");
            var $expire = $("#access-" + id + "-expire");

            if ($el.is(':checked')) {
                $inputBlock.show();
                $accessFlags.prop("disabled", false);
                $expire.prop("disabled", false);
            } else {
                if ($el.attr("data-id")) {
                    if (!confirm('Удалить привилегию?')) {
                        $el.prop("checked", true);
                        return false;
                    } else {
                        var userId = $el.attr("data-id").split('-')[0];
                        var serverId = $el.attr("data-id").split('-')[1];
                        this.deletePrivilege(userId, serverId);
                        $el.attr("data-id", "");
                    }
                }
                $accessFlags.prop("disabled", true);
                $expire.prop("disabled", true);
                $inputBlock.hide();
            }
        },
        deletePrivilege: function (userId, serverId) {
            $.ajax({
                url: "<?= Url::toRoute(['users/delete-privilege']) ?>?userId=" + userId + "&serverId=" + serverId,
                success: function (response) {
                    return true;
                },
                error: function () {
                    alert('Произошла ошибка при удалении.');
                    return false;
                }
            });
        }
    }
</script>