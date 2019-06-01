<?php

use app\install\models\SettingsForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model SettingsForm */

?>

<?php $form = ActiveForm::begin(['id' => 'install-settings']); ?>

<div class="card mx-auto" style="max-width: 400px">
    <div class="card-header">Настройка сайта</div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'adminEmail')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'supportEmail')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'apiKey')->textInput(['maxlength' => true]) ?>

            <p class="text-right">
                <?= Html::submitButton('Завершить', ['class' => 'btn btn-dark']) ?>
            </p>
        </li>
    </ul>
</div>

<?php ActiveForm::end(); ?>
