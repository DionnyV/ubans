<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\BanSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<a class="btn btn-secondary mb-3" data-toggle="collapse" href="#collapseBansSearch" role="button" aria-expanded="false"
   aria-controls="collapseBansSearch">Поиск</a>
<div class="collapse" id="collapseBansSearch">
    <div class="bans-search">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <?= $form->field($model, 'player_ip') ?>
        <?= $form->field($model, 'player_id') ?>
        <?= $form->field($model, 'player_nick') ?>
        <?= $form->field($model, 'admin_nick') ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
