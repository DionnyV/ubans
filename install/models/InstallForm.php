<?php

namespace app\install\models;

use app\install\services\InstallService;
use Yii;
use yii\base\Model;
use yii\db\Connection;

/**
 * Модель формы страницы установки.
 */
class InstallForm extends Model
{
    public $host = 'localhost';
    public $dbName = '';
    public $username = '';
    public $password = '';
    public $tablePrefix = 'amx_';

    /**
     * @var InstallService
     */
    private $installService;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['host', 'dbName', 'username', 'password', 'tablePrefix'], 'required'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->installService = Yii::$container->get(InstallService::class);
        parent::init();
    }

    public function connect()
    {
        $db = new Connection([
            'dsn' => "mysql:host=$this->host;dbname=$this->dbName",
            'username' => $this->username,
            'password' => $this->password,
            'charset' => 'utf8',
        ]);
        try {
            $this->installService->checkDbConnection($db);
            return true;
        } catch (\Throwable $e) {
            switch ($e->getCode()) {
                case 2002:
                    $this->addError('host', 'Ошибка подключения к базе данных.');
                    break;
                case 1045:
                    $this->addError('username', 'Проверьте правильность указанных данных.');
                    $this->addError('password', 'Проверьте правильность указанных данных.');
                    break;
                case 1049:
                    $this->addError('dbName', 'База данных не найдена.');
                    break;
                default:
                    $this->addError('host', 'Ошибка подключения к базе данных. Проверьте все данные.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'host' => 'Адрес',
            'dbName' => 'Название базы данных',
            'username' => 'Имя пользователя',
            'password' => 'Пароль',
            'tablePrefix' => 'Префикс базы данных',
        ];
    }
}
