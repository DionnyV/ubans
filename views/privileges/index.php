<?php

use app\models\Privilege;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PrivilegeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Privileges');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Settings')];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="privilege-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-dark']) ?>
    </p>

    <?= GridView::widget([
        'id' => 'privileges',
        'layout' => '{items}{pager}',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            [
                'attribute' => 'server_id',
                'value' => function (Privilege $data) {
                    return $data->server->hostname;
                },
            ],
            'access_flags',
        ],
    ]); ?>

</div>
<?php
$this->registerJs(
    '$(function(){
            $("#privileges tbody").on("click", "tr", function(){
                var id = $(this).attr("data-key");
                document.location.href = "' . Url::to(['privileges/update']) . '?id=" + id;
            });
    });'
);