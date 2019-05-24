<?php

namespace app\widgets;

use app\models\search\BanSearch;
use yii\base\Widget;

class BansSearch extends Widget
{
    public $ban;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->ban = new BanSearch();
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        return $this->render('bans-search', ['model' => $this->ban]);
    }
}
