<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\BanForm */

$this->title = Yii::t('app', 'Редактировать бан: {name}', [
    'name' => $model->ban->player_nick,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bans-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
