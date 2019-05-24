<?php

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
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\form\UserUpdateForm */

$user = $model->userForm->user;
$privileges = $model->privilegesForm->privileges;
?>
<div class="user">
    <?php Pjax::begin(); ?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
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
                    <?= $form->field($user, 'auth_key', ['template' => '
                           {label}
                           <div class="input-group">
                              {input}
                              <div class="input-group-append">
                                <span class="input-group-text" onclick="UserUpdateForm.generateAuthKey()">#</span>
                              </div>
                               {error}{hint}
                           </div>
                           <small class="form-text text-muted"></small>
                       '])->textInput(['maxlength' => true]); ?>
                    <?= $form->field($model->userForm, 'role')->dropdownList(RoleReference::getRoles()) ?>
                    <?= $form->field($user, 'status')->dropdownList(User::getStatusLabels()) ?>

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
                    <?= $form->field($model->userForm, 'flag')->dropdownList($user->getAccountFlags()) ?>
                    <?= $form->field($model->userForm, 'options')->checkboxList($user->getAccountOptions()) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                Привилегии:
            </div>
            <div class="card-body">
                <?php foreach ($privileges as $privilege) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="Access[<?= $privilege->server_id ?>][enable]"
                               id="server-<?= $privilege->server_id ?>"
                               onchange="UserUpdateForm.toggle(this)"
                            <?php if (!empty($privilege->expire)) : ?>
                                data-id="<?= $privilege->user_id . '-' . $privilege->server_id ?>"
                                checked
                            <?php endif; ?>
                        >
                        <label class="form-check-label" for="server-<?= $privilege->server_id ?>">
                            <?= $privilege->server->hostname ?>
                        </label>
                    </div>
                    <span id="privilegeServer<?= $privilege->server_id ?>"
                          style="display:<?= !empty($privilege->expire) ? 'block' : 'none' ?>;">
                        <?= $form->field($privilege, "[$privilege->server_id]access_flags")
                            ->dropdownList(ServerService::getPrivilegesList($privilege->server)) ?>

                        <?= $form->field($privilege, "[$privilege->server_id]expire")
                            ->widget(DateTimePicker::class, [
                                'type' => DateTimePicker::TYPE_INPUT,
                                'options' => [
                                    'placeholder' => 'Ввод даты/времени...',
                                    'value' => Yii::$app->formatter->asDatetime($privilege->expire ?? time(), 'dd.MM.Y hh:mm'),
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
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $user->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
<script>
    var UserUpdateForm = {
        generateAuthKey: function () {
            var randomKey = Math.random().toString(36).substr(2) + '_' + Math.random().toString(36).substr(2);

            $("input[name='User[auth_key]'").val(randomKey);
        },
        toggle: function (el) {
            el = $(el);
            var inputBlockId = $("#privilegeServer" + el.attr('id').split('-')[1]);
            if (el.is(':checked')) {
                inputBlockId.show();
            } else {
                if (el.attr("data-id")) {
                    if (!confirm('Удалить привилегию?')) {
                        el.prop("checked", true);
                        return false;
                    } else {
                        var userId = el.attr("data-id").split('-')[0];
                        var serverId = el.attr("data-id").split('-')[1];
                        this.deletePrivilege(userId, serverId);
                        el.attr("data-id", "");
                    }
                }
                inputBlockId.hide();
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