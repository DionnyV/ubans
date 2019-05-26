<?php

use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Server */

$this->title = Yii::t('app', '{name}', [
    'name' => $model->hostname,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Servers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$playersCount = 0;
?>
<div class="server-view">
    <div class="card-deck">
        <div class="card" style="max-width: 350px">
            <img src="http://bratm.najky.webnoviny.sk/cache/medium/uploads/2015/04/counterstrike-dust.jpg?i=31541"
                 class="card-img-top">
            <div class="card-body">
                <h5><?= Html::encode($model->hostname) ?></h5>
                <h6 class="card-subtitle text-muted"><?= Html::encode($info['ModDesc']) ?></h6>
                <p class="my-2">
                    <a href="steam://connect/<?= Html::encode($model->address) ?>"
                       class="card-text"><?= Html::encode($model->address) ?></a>
                </p>
                <div class="progress" style="height: 25px; position: relative;">
                    <span style="position: absolute; line-height: 25px; width: 100%; text-align: center;">
                        <?= Html::encode($info['Players']) ?> /
                        <?= Html::encode($info['MaxPlayers']) ?>
                    </span>
                    <div class="progress-bar progress-bar-striped <?php
                    if ($info['OnlineInPercents'] <= 50) {
                        echo 'bg-success';
                    } elseif ($info['OnlineInPercents'] > 50 && $info['OnlineInPercents'] < 90) {
                        echo 'bg-warning';
                    } else {
                        echo 'bg-danger';
                    }
                    ?>"
                         role="progressbar"
                         style="width: <?= $info['OnlineInPercents'] ?>%"
                         aria-valuenow="<?= $info['OnlineInPercents'] ?>"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>
                <p class="card-text text-right">
                    <small class="text-muted"><?= Html::encode($info['Map']) ?></small>
                </p>
            </div>
        </div>
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
                <?php foreach ($info['PlayersInfo'] as $player) : ?>
                    <tr>
                        <th scope="row"><?= ++$playersCount ?></th>
                        <td><?= $player['Name'] ?></td>
                        <td><?= $player['Frags'] ?></td>
                        <td><?= $player['TimeF'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
