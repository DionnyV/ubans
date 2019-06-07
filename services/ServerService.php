<?php

namespace app\services;

use app\models\Server;
use app\services\dto\ServerInfoData;
use Exception;
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

    /**
     * {@inheritDoc}
     */
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
     * @return ServerInfoData[]
     */
    public function getServersInfo(): array
    {
        $data = [];
        foreach (Server::find()->all() as $server) {
            $data[$server->id] = $this->getServerInfo($server);
        }
        return $data;
    }

    /**
     * Возвращает объект данных о сервере.
     *
     * @param Server $server
     * @return ServerInfoData
     */
    public function getServerInfo(Server $server): ServerInfoData
    {
        $serverInfo = new ServerInfoData();
        $serverInfo->id = $server->id;
        $serverInfo->hostname = $server->hostname;
        $serverInfo->ip = $this->getServerIp($server);
        $serverInfo->port = $this->getPort($server);
        $serverInfo->address = $server->address;

        try {
            $this->sourceQuery->Connect($serverInfo->ip, $serverInfo->port, 1, $this->sourceQuery::GOLDSOURCE);
            $info = $this->sourceQuery->GetInfo();
            $players = $this->sourceQuery->GetPlayers();
            $serverInfo->online = $info['Players'];
            $serverInfo->maxPlayers = $info['MaxPlayers'];
            $serverInfo->onlinePercents = $this->calculateOnlineInPercents(
                $serverInfo->online,
                $serverInfo->maxPlayers
            );
            $serverInfo->map = $info['Map'];
            $serverInfo->modDesc = $info['ModDesc'];
            $serverInfo->playersInfo = $players;
        } catch (Exception $e) {
            // игнорируем ошибки
        } finally {
            $this->sourceQuery->Disconnect();
        }
        return $serverInfo;
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
     * Возвращает кол-во игроков в процентах.
     *
     * @param int $currentPlayers
     * @param int $maxPlayers
     * @return int
     */
    private function calculateOnlineInPercents(int $currentPlayers, int $maxPlayers): int
    {
        return round($currentPlayers * 100 / $maxPlayers);
    }

    /**
     * Возвращает IP адрес сервера.
     *
     * @param Server $server
     * @return mixed
     */
    private function getServerIp(Server $server)
    {
        $ip = '';
        if (strpos($server->address, ':')) {
            $ip = explode(':', $server->address)[0];
        }

        return $ip;
    }

    /**
     * Возвращает порт сервера.
     *
     * @param Server $server
     * @return mixed
     */
    private function getPort(Server $server)
    {
        $port = '';
        if (strpos($server->address, ':')) {
            $port = explode(':', $server->address)[1];
        }
        return $port;
    }
}
