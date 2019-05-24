<?php

use app\models\Privilege;
use app\models\Server;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\PrivilegeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Privileges');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="privilege-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
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
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        return \yii\bootstrap4\Html::a('Ред.', $url, [
                            'title' => 'Редактировать',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('Del.', $url, [
                            'title' => 'Удалить',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
