<?php

use app\services\BanService;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\form\BanForm */

$this->title = Yii::t('app', 'Информация о бане: {name}', [
    'name' => $model->player_nick,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$banActive = BanService::isActive($model);

?>
<div class="ban-view">
    <div class="alert alert-<?= $banActive ? 'warning' : 'secondary'?> alert-dismissible fade show" role="alert">
        Игрок <strong><?= $banActive ? 'забанен' : 'разбанен'?> </strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <table class="table table-striped table-borderless">
        <tbody>
        <tr class="table-active">
            <th scope="row">Ник игрока:</th>
            <td><?= Html::encode($model->player_nick) ?></td>
        </tr>
        <tr class="table-active">
            <th scope="row">Steam ID игрока:</th>
            <td><?= Html::encode($model->player_id) ?></td>
        </tr>
        <?php if (Yii::$app->user->can('manageBans')) : ?>
            <tr class="table-active">
                <th scope="row">IP игрока:</th>
                <td><?= Html::encode($model->player_ip) ?></td>
            </tr>
            <tr class="table-active">
                <th scope="row">Steam ID админа:</th>
                <td><?= Html::encode($model->admin_id) ?></td>
            </tr>
            <tr class="table-active">
                <th scope="row">IP админа:</th>
                <td><?= Html::encode($model->admin_ip) ?></td>
            </tr>
        <?php endif; ?>
        <tr class="table-active">
            <th scope="row">Забанен админом:</th>
            <td><?= Html::encode($model->admin_nick) ?></td>
        </tr>
        <tr class="table-active">
            <th scope="row">На сервере:</th>
            <td><?= Html::encode($model->server_name) ?></td>
        </tr>
        <tr class="table-active">
            <th scope="row">Причина бана:</th>
            <td><?= Html::encode($model->ban_reason) ?></td>
        </tr>
        <tr class="table-active">
            <th scope="row">Дата бана:</th>
            <td><?= date('d.m.Y H:i', $model->ban_created) ?></td>
        </tr>
        <tr class="table-active">
            <th scope="row">Бан истекает:</th>
            <td><?= BanService::getExpireData($model) ?></td>
        </tr>
        </tbody>
    </table>
    <?php if (Yii::$app->user->can('manageBans')) : ?>
        <div class="form-group text-right">
            <?php if ($banActive) : ?>
                <a class="btn btn-light" href="<?= \yii\helpers\Url::to(['bans/unban', 'id' => $model->id])?>"
                   role="button">Разбанить</a>
            <?php endif; ?>
            <a class="btn btn-dark" href="<?= \yii\helpers\Url::to(['bans/update', 'id' => $model->id])?>"
               role="button">Редактировать</a>
        </div>
    <?php endif; ?>
</div>
