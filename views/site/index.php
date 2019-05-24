<?php

use app\widgets\BansSearch;
use app\widgets\ServersOnline;

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<section class="jumbotron text-center">
    <div class="container">
        <h1 class="jumbotron-heading">Album example</h1>
        <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator,
            etc. Make it short and sweet, but not too short so folks don't simply skip over it entirely.</p>

        <p>
            <a href="#" class="btn btn-primary my-2">Список банов</a>
            <a href="#" class="btn btn-secondary my-2">Найти</a>
        </p>
    </div>
</section>
<div class="site-index">
    <div class="body-content">
        <div class="card-deck">
            <?= ServersOnline::widget() ?>
            <?= BansSearch::widget() ?>
        </div>
    </div>
</div>
