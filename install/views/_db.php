<?php

use app\install\models\DbForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $form \yii\widgets\ActiveForm */
/* @var $model DbForm */

?>
<?php $form = ActiveForm::begin(['id' => 'install']); ?>

<div class="card mx-auto" style="max-width: 400px">
    <div class="card-header">Настройка подключения к БД</div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <?= $form->field($model, 'host')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'dbName')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'tablePrefix')->textInput(['maxlength' => true]) ?>

            <p class="text-right">
                <?= Html::submitButton('Далее', ['class' => 'btn btn-dark']) ?>
            </p>
        </li>
    </ul>
</div>
<?php ActiveForm::end(); ?>