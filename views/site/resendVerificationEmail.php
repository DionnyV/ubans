<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model ResetPasswordForm */

use frontend\models\form\ResetPasswordForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Переслать код подтверждения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-resend-verification-email">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста, введите вашу электронную почту. На нее будет отправлено письмо с подтверждением.</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>

            <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
