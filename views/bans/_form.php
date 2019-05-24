<?php

use kartik\datetime\DateTimePicker;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\form\BanForm */
/* @var $form yii\widgets\ActiveForm */

$ban = $model->ban;

$date = new \DateTime();
$date->setTimestamp($ban->ban_created);
$date->modify($ban->ban_length . 'min');

$isBanActive = true;
if ($ban->ban_length === -1 || $ban->ban_length !== 0 && $date->getTimestamp() < time()) {
    $isBanActive = false;
}
?>

<div class="ban-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="card-deck">
        <div class="card">
            <div class="card-header">
                Информация о игроке
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($ban, 'player_nick')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                    <?= $form->field($ban, 'player_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                    <?= $form->field($ban, 'player_ip')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                Информация о бане
                <?php if (!$isBanActive) : ?>
                    <span class="badge badge-success">Разбанен</span>
                <?php else : ?>
                    <span class="badge badge-danger">Забанен</span>
                <?php endif; ?>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($ban, 'ban_reason')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'until')
                        ->widget(DateTimePicker::class, [
                            'type' => DateTimePicker::TYPE_INPUT,
                            'options' => [
                                'placeholder' => 'Ввод даты/времени...',
                                'value' => is_int($model->until) ?
                                    Yii::$app->formatter->asDatetime($model->until ?? time(), 'dd.MM.Y hh:mm') :
                                    $model->until,
                            ],
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd.MM.yyyy hh:i',
                                'startDate' => Yii::$app->formatter->asDatetime(strtotime('- 1 day'), 'dd.MM.Y hh:mm'),
                                'autoclose' => true,
                                'weekStart' => 1,
                                'todayHighlight' => true,
                                'todayBtn' => true,
                            ],
                        ]); ?>

                    <div class="form-group">
                        <?= $form->field($ban, 'ban_created')->textInput([
                                'value' => date('d.m.Y h:m', $ban->ban_created),
                                'maxlength' => true,
                                'disabled' => true,
                        ]) ?>
                    </div>

                    <?= $form->field($ban, 'server_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                </li>
            </ul>
        </div>

        <div class="card">
            <div class="card-header">
                Информация об Админе
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($ban, 'admin_nick')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                    <?= $form->field($ban, 'admin_id')->textInput(['maxlength' => true, 'disabled' => true]) ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="form-group text-right mt-3">
        <?php if ($isBanActive) : ?>
            <?= Html::a(Yii::t('app', 'Unban'), ['unban', 'id' => $ban->id], ['class' => 'btn btn-light']) ?>
        <?php endif; ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
