<?php

use app\models\Ban;
use app\services\BanService;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $form \yii\widgets\ActiveForm */

?>
<div class="card">
    <div class="card-body">
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
    </div>
</div>
<?php
$this->registerJs(
    '$(function(){
            $("#bans tbody").on("click", "tr", function(){
                var id = $(this).attr("data-key");
                document.location.href = "' . Url::to(['bans/view']) . '?id=" + id;
            });
    });'
);