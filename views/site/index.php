<?php

use app\widgets\BansList;
use app\widgets\BansSearch;
use app\widgets\ServersOnline;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-9 mb-3">
            <?= BansList::widget() ?>
        </div>
        <div class="col-lg-3">
            <?= BansSearch::widget() ?>
            <?= ServersOnline::widget() ?>
        </div>
    </div>
</div>
