<?php

namespace app\services\dto;

/**
 * Информация о сервере.
 */
class ServerInfoData
{
    /**
     * @var integer Идентификатор сервера.
     */
    public $id;

    /**
     * @var string Название сервера.
     */
    public $hostname = 'Нет информации';

    /**
     * @var string IP сервера.
     */
    public $ip = 'Нет информации';

    /**
     * @var string Порт сервера.
     */
    public $port = 'Нет информации';

    /**
     * @var string Полный адрес (IP + порт).
     */
    public $address = 'Нет информации';

    /**
     * @var string Онлайн.
     */
    public $online = 0;

    /**
     * @var int Максимальное кол-во игроков.
     */
    public $maxPlayers = 0;

    /**
     * @var int Заполненость сервера в процентах.
     */
    public $onlinePercents = 0;

    /**
     * @var string Карта.
     */
    public $map = 'Нет информации';

    /**
     * @var string Описание мода.
     */
    public $modDesc = 'Нет информации';

    /**
     * @var array Список игроков.
     */
    public $playersInfo = [];
}
