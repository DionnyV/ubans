<?php

/* @var $this \yii\web\View */
/* @var $action string */
/* @var $content string */

use app\widgets\Alert;
use yii\bootstrap4\Html;
use app\assets\AppAsset;

AppAsset::register($this);
$this->title = 'Установка сайта';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="container py-4">
    <?= Alert::widget() ?>
    <div class="install">
        <?= $this->render($action, [
            'model' => $model
        ]) ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
