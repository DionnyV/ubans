<?php

namespace app\install\services;

use app\install\models\InstallForm;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Application;
use yii\db\Connection;
use yii\db\Exception;

/**
 * Сервис установки сайта.
 */
class InstallService
{
    /**
     * Выполняет установку сайта.
     *
     * @param InstallForm $form
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    public function install(InstallForm $form)
    {
        $this->createDbConfig($form);
        $this->migrate();
    }

    /**
     * Проверяет статус установки сайта.
     */
    public function checkInstallation()
    {
        //todo реализовать метод проверки статуса установки сайта.
    }

    /**
     * Проверяет подключение к базе данных.
     *
     * @param Connection $connection
     * @throws Exception
     */
    public function checkDbConnection(Connection $connection)
    {
        $connection->open();
        if ($connection === null) {
            throw new Exception('Ошибка подключения к базе данных.');
        }
    }

    /**
     * Создает конфигурационный файл.
     *
     * @param InstallForm $form
     * @throws \Exception
     */
    private function createDbConfig(InstallForm $form): void
    {
        $dbFile = Yii::getAlias('@app/config/db.php');
        if (!is_writable(dirname($dbFile)) || !file_put_contents($dbFile, $this->prepareConfigFile($form))) {
            throw new \Exception('Не удалось создать конфиг.');
        }
    }

    /**
     * Выполняет миграции.
     *
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     */
    private function migrate()
    {
        ob_start();
        $oldApp = Yii::$app;
        $config = require Yii::getAlias('@app/config/console.php');
        new Application($config);
        Yii::$app->runAction('migrate', [
            'migrationPath' => '@app/install/migrations/',
            'interactive' => false
        ]);
        Yii::$app = $oldApp;
        ob_clean();
    }

    /**
     * Создает роли и разрешения.
     *
     * @throws \yii\base\Exception
     */
    private function createRolesAndPermissions(): void
    {
        $auth = Yii::$app->authManager;

        $manageBans = $auth->createPermission('manageBans');
        $manageBans->description = 'Управление банами';
        $auth->add($manageBans);

        $manageContent = $auth->createPermission('manageContent');
        $manageContent->description = 'Управление контентом';
        $auth->add($manageContent);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Управление пользователями';
        $auth->add($manageUsers);

        $manageSettings = $auth->createPermission('manageSettings');
        $manageSettings->description = 'Управление настройками';
        $auth->add($manageSettings);

        $editor = $auth->createRole('editor');
        $auth->add($editor);
        $auth->addChild($editor, $manageContent);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manageBans);

        $deputy = $auth->createRole('deputy');
        $auth->add($deputy);
        $auth->addChild($deputy, $editor);
        $auth->addChild($deputy, $admin);

        $root = $auth->createRole('root');
        $auth->add($root);
        $auth->addChild($root, $manageSettings);
        $auth->addChild($root, $deputy);
    }

    /**
     * Подготавливает информацию для записи в конфиг.
     *
     * @param InstallForm $form
     * @return string
     */
    private function prepareConfigFile(InstallForm $form): string
    {
        return "<?php\n\nreturn [\n   'class' => 'yii\db\Connection',\n" .
            "   'dsn' => 'mysql:host=" . $form->host . ";dbname=" . $form->dbName . "',\n" .
            "   'username' => '" . $form->username . "',\n" .
            "   'password' => '" . $form->password . "',\n" .
            "   'charset' => 'utf8',\n" .
            "   'tablePrefix' => '" . $form->tablePrefix . "',\n];\n";
    }
}
