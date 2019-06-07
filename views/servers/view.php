<?php

use app\services\dto\ServerInfoData;
use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Server */
/* @var $server ServerInfoData */

$this->title = Yii::t('app', '{name}', [
    'name' => $model->hostname,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$playersCount = 0;
?>
<div class="server-view">
    <div class="row">
        <div class="col-lg-3 mb-3">
            <div class="card">
                <img src="https://tsarvar.com/maps/cs1.6/<?= Html::encode($server->map) ?>/2.jpg"
                     class="card-img-top">
                <div class="card-body">
                    <h5><?= Html::encode($model->hostname) ?></h5>
                    <h6 class="card-subtitle text-muted"><?= Html::encode($server->modDesc) ?></h6>
                    <p class="my-2">
                        <a href="steam://connect/<?= Html::encode($model->address) ?>"
                           class="card-text"><?= Html::encode($model->address) ?></a>
                    </p>
                    <p class="card-text"><?= Html::encode($model->description) ?></p>
                    <div class="progress" style="height: 25px; position: relative;">
                    <span style="position: absolute; line-height: 25px; width: 100%; text-align: center;">
                        <?= Html::encode($server->online) . '/' . Html::encode($server->maxPlayers) ?>
                    </span>
                        <div class="progress-bar progress-bar-striped <?php
                        if ($server->onlinePercents <= 50) {
                            echo 'bg-success';
                        } elseif ($server->onlinePercents > 50 && $server->onlinePercents < 90) {
                            echo 'bg-warning';
                        } else {
                            echo 'bg-danger';
                        }
                        ?>"
                             role="progressbar"
                             style="width: <?= $server->onlinePercents ?>%"
                             aria-valuenow="<?= $server->onlinePercents ?>"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                    <p class="card-text text-right">
                        <small class="text-muted"><?= Html::encode($server->map) ?></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless table-striped">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Ник</th>
                            <th scope="col">Фраги</th>
                            <th scope="col">Играет</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($server->playersInfo)) : ?>
                            <?php foreach ($server->playersInfo as $player) : ?>
                                <tr>
                                    <th scope="row"><?= ++$playersCount ?></th>
                                    <td><?= Html::encode($player['Name']) ?></td>
                                    <td><?= Html::encode($player['Frags']) ?></td>
                                    <td><?= Html::encode($player['TimeF']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td class="text-center" colspan="4">Нет информации.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group text-right mt-3">
        <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-dark']) ?>
        <?php if (Yii::$app->user->can('manageSettings')) : ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?php endif; ?>
    </div>
</div>
