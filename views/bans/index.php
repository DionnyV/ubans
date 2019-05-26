<?php

use app\models\Ban;
use app\services\BanService;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BanSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bans');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bans-index">
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'layout' => '{items}{pager}',
        'id' => 'bans',
        'dataProvider' => $dataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            if (!BanService::isActive($model)) {
                return ['class' => 'table-secondary'];
            }
        },
        'columns' => [
            [
                'attribute' => 'ban_created',
                'format' => ['date', 'php:d.m.Y']
            ],
            'player_nick',
            'admin_nick',
            'ban_reason',
            [
                    'attribute' => 'expired',
                    'label' => 'Истекает',
                    'value' => function (Ban $data) {
                        return BanService::getExpireData($data);
                    }
            ],
        ],
        'tableOptions' => ['class' => 'table table-borderless table-striped table-responsive-md'],
        'summary' => null,
        'pager' => [
            'options'=>['class'=>'pagination justify-content-center'],
            'prevPageCssClass' => 'page-item',
            'pageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'linkOptions' => ['class' => 'page-link'],
            'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link']
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
$this->registerJs(
    '$(function(){
            $("#bans").on("click", "tr", function(){
                var banId = $(this).attr("data-key");
                document.location.href = "' . Url::to(['bans/view']) . '?id=" + banId;
            });
    });'
);
