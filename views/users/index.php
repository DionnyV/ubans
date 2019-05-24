<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\UserSearch */
/* @var $userService app\services\UserService */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'email',
            'nickname',
            [
                'attribute' => 'flags',
                'value' => function ($data) use ($userService) {
                    return $userService->formatAccountFlagsString($data);
                },
            ],
        ],
    ]); ?>
</div>
<?php
$this->registerJs(
    '$(function(){
            $("tr").on("click", function(){
                var banId = $(this).attr("data-key");
                document.location.href = "' . \yii\helpers\Url::to(['users/update']) . '?id=" + banId;
            });
    });'
);