<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

/* @var $model app\models\search\BanSearch */
/* @var $form \yii\widgets\ActiveForm */

?>
<div class="card" style="max-width: 320px">
    <div class="card-body">
        <?php $form = ActiveForm::begin([
            'action' => ['bans/index'],
            'method' => 'get',
        ]); ?>
        <h3 class="card-title">Найти Бан</h3>
        <div class="form-group">
            <?= $form->field($model, 'player_id')->label('Поиск по SteamID') ?>
            <?= $form->field($model, 'player_nick')->label('Поиск по нику') ?>
        </div>
        <p class="d-flex justify-content-end align-items-center">
            <a href="<?= Url::to(['bans/index']) ?>" class="card-link mr-2">cписок банов</a>
            <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        </p>
        <?php ActiveForm::end(); ?>
    </div>
</div>