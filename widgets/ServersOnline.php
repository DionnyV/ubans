<?php

namespace app\widgets;

use app\services\ServerService;
use yii\base\Widget;

class ServersOnline extends Widget
{
    /**
     * @var ServerService
     */
    private $serverService;
    private $servers;

    public function __construct(ServerService $serverService, $config = [])
    {
        $this->serverService = $serverService;
        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->servers = $this->serverService->getServersInfo();
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        return $this->render('servers', ['servers' => $this->servers]);
    }
}
