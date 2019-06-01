<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Privilege */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="server-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="card-deck mb-3">
        <div class="card">
            <div class="card-header">Отображение на сайте:</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($model, 'hostname')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'description')->textarea(['maxlength' => true]) ?>
                </li>
            </ul>
        </div>
        <div class="card">
            <div class="card-header">Сервер</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'amxban_motd')->textInput(['maxlength' => true]) ?>
                </li>
            </ul>
        </div>
    </div>
    <div class="form-group text-right">
        <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-light']) ?>
        <?php if (!$model->isNewRecord) : ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-dark']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
