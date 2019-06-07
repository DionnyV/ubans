<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\bootstrap4\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
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

    <?= $this->render('_menu') ?>

    <div class="container py-4">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
            'activeItemTemplate' => "<li class=\"breadcrumb-item active\" aria-current=\"page\">{link}</li>\n",
            'options' => [
                'class' => 'breadcrumb'
            ],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left"><?= Yii::$app->name ?> <?= date('Y') ?></p>
            <span class="site-version" style="display: none">
            <p>Доступна новая версия! <a href="//ubans.ru/changelog">Посмотреть что нового.</a></p>
        </span>
        </div>
    </footer>
    <?php
    $version = Yii::$app->params["version"] ?? '0';
    $this->registerJs('$(function(){
            setTimeout(function(){ 
                $.ajax({
                  type: "POST",
                  url: "https://ubans.ru/api/check-updates",
                  data: {
                    version: "' . $version . '",
                  },
                  success: function(result) {
                    console.log(result.data);
                  }
                });
            }, 2000);
    });');
    ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
<?php