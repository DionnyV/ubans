<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\ServerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Servers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servers-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'hostname',
            'address',
            [
                'attribute' => 'online',
                'label' => 'Онлайн',
                'value' => function ($data) {
                    return '25/32';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('View', $url, [
                            'title' => 'Посмотреть',
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
