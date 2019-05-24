<?php

use app\models\form\UserPrivilegesForm;
use app\services\ServerService;
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model UserPrivilegesForm */

$privileges = $model->privileges;

?>
<div class="user">
    <?php Pjax::begin(); ?>
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->errorSummary($privileges) ?>
    <div class="card-deck mb-3">
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
                               onchange="PrivilegeForm.toggle(this)"
                            <?php if (!empty($privilege->expire)) : ?>
                                data-id="<?= $privilege->user_id . '-' . $privilege->server_id?>"
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

                        <?php
                        echo $form->field($privilege, "[$privilege->server_id]expire")
                            ->widget(DateTimePicker::class, [
                                'type' => DateTimePicker::TYPE_INPUT,
                                'value' => date('dd.MM.yyyy hh:i'),
                                'options' => [
                                    'placeholder' => 'Ввод даты/времени...',
                                    'value' => Yii::$app->formatter->asDatetime($privilege->expire, 'dd.MM.Y hh:mm'),
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

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
<script>
    var PrivilegeForm = {
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
                success: function(response) {
                    return true;
                },
                error: function() {
                    alert('Произошла ошибка при удалении.');
                    return false;
                }
            });
        }
    }
</script>