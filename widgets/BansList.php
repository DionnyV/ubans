<?php

namespace app\widgets;

use app\models\search\BanSearch;
use Yii;
use yii\base\Widget;

class BansList extends Widget
{
    public $dataProvider;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $searchModel = new BanSearch();
        $this->dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        return $this->render('bans-list', ['dataProvider' => $this->dataProvider]);
    }
}
