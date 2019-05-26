<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Servers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servers-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'layout' => '{items}',
        'id' => 'servers',
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'hostname',
            'address',
        ],
        'pager' => false
    ]); ?>
</div>
<?php
$this->registerJs(
    '$(function(){
            $("#servers tbody").on("click", "tr", function(){
                var id = $(this).attr("data-key");
                document.location.href = "' . Url::to(['servers/view']) . '?id=" + id;
            });
    });'
);

