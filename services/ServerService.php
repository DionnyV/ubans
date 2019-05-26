<?php

namespace app\services;

use app\models\Server;
use Exception;
use xPaw\SourceQuery\Exception\InvalidArgumentException;
use xPaw\SourceQuery\Exception\InvalidPacketException;
use xPaw\SourceQuery\Exception\TimeoutException;
use xPaw\SourceQuery\SourceQuery;
use yii\helpers\ArrayHelper;

/**
 * Сервис сервера.
 */
class ServerService
{
    /**
     * @var SourceQuery
     */
    private $sourceQuery;
    private $serversInfo = [];

    public function __construct(SourceQuery $sourceQuery)
    {
        $this->sourceQuery = $sourceQuery;
    }

    /**
     * Находит сервер по идентификатору.
     *
     * @param $id
     * @return Server
     * @throws Exception
     */
    public function findById($id): Server
    {
        $model = Server::findOne($id);
        if ($model === null) {
            throw new Exception('Сервер не найден.');
        }
        return $model;
    }

    /**
     * Возвращает информацию по всем серверам.
     *
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidPacketException
     * @throws TimeoutException
     */
    public function getServersInfo()
    {
        $data = [];
        foreach (Server::find()->all() as $server) {
            $data[] = [
                'hostname' => $server->hostname,
                'ip' => $server->address,
                'online' => $this->getOnline($server),
                'onlinePercents' => $this->getOnlineInPercents($server)
            ];
        }
        return $data;
    }

    /**
     * Возвращает список привилегий сервера.
     *
     * @param Server $server
     * @return array
     */
    public static function getPrivilegesList(Server $server)
    {
        return ArrayHelper::map(
            $server->privileges,
            'access_flags',
            'name'
        );
    }

    /**
     * Возвращает список серверов.
     *
     * @return array
     */
    public static function getServersList()
    {
        return ArrayHelper::map(
            Server::find()->all(),
            'id',
            'hostname'
        );
    }

    /**
     * Возвращает онлайн сервера.
     *
     * @param Server $server
     * @return string
     * @throws InvalidArgumentException
     * @throws InvalidPacketException
     * @throws TimeoutException
     */
    public function getOnline(Server $server)
    {
        $info = $this->getInfo($server);

        if ($info) {
            return $info['Players'] . '/' . $info['MaxPlayers'];
        }
        return 'Нет информации.';
    }

    /**
     * Возвращает информацию о сервере.
     *
     * @param Server $server
     * @return array|bool
     * @throws InvalidArgumentException
     * @throws InvalidPacketException
     * @throws TimeoutException
     */
    public function getInfo(Server $server)
    {
        if (!isset($this->serversInfo[$server->id]) || empty($this->serversInfo[$server->id])) {
            $ip = $this->getServerIp($server);
            $port = $this->getPort($server);
            $this->sourceQuery->Connect($ip, $port, 1, $this->sourceQuery::GOLDSOURCE);
            $this->serversInfo[$server->id] = $this->sourceQuery->GetInfo();
            $this->serversInfo[$server->id]['PlayersInfo'] = $this->sourceQuery->GetPlayers();
            $this->serversInfo[$server->id]['OnlineInPercents'] = $this->getOnlineInPercents($server);
            $this->sourceQuery->Disconnect();
        }
        return $this->serversInfo[$server->id];
    }

    /**
     * Возвращает кол-во игроков в процентах.
     *
     * @param Server $server
     * @return int
     * @throws InvalidArgumentException
     * @throws InvalidPacketException
     * @throws TimeoutException
     */
    private function getOnlineInPercents(Server $server): int
    {
        $info = $this->getInfo($server);

        if ($info) {
            return round($info['Players'] * 100 / $info['MaxPlayers']);
        }
        return 0;
    }

    /**
     * Возвращает IP адрес сервера.
     *
     * @param Server $server
     * @return mixed
     */
    private function getServerIp(Server $server)
    {
        return explode(':', $server->address)[0];
    }

    /**
     * Возвращает порт сервера.
     *
     * @param Server $server
     * @return mixed
     */
    private function getPort(Server $server)
    {
        return explode(':', $server->address)[1];
    }
}
