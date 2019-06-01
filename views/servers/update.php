<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Server */

$this->title = Yii::t('app', 'Update: {modelClass}', [
    'modelClass' => $model->hostname,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="server-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
