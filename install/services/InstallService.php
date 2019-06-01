<?php

namespace app\install\services;

use app\install\models\DbForm;
use app\install\models\SettingsForm;
use app\models\User;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Application;
use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\FileHelper;

/**
 * Сервис установки сайта.
 */
class InstallService
{
    /**
     * Выполняет установку сайта.
     *
     * @param DbForm $form
     * @throws InvalidConfigException
     * @throws InvalidRouteException
     * @throws \yii\console\Exception
     * @throws \Exception
     */
    public function install(DbForm $form): void
    {
        if ($this->checkInstallation()) {
            throw new \Exception('Сайт уже установлен.');
        }
        $this->createDbConfig($form);
        $this->migrate();
    }

    /**
     * Создаем первоначальные настройки сайта.
     *
     * @param SettingsForm $form
     * @throws \Exception
     */
    public function createSiteSettings(SettingsForm $form): void
    {
        $this->createSettingsConfig($form);
    }

    /**
     * Завершает установку.
     *
     * @throws \Exception
     */
    public function finishInstallation()
    {
        $this->deleteInstallFiles();
    }

    /**
     * Проверяет статус установки сайта.
     *
     * @return bool
     */
    public function checkInstallation(): bool
    {
        if (file_exists(Yii::getAlias('@app/config/db.php'))) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет наличие настроек сайта.
     *
     * @return bool
     */
    public function checkSettingsExist(): bool
    {
        if (file_exists(Yii::getAlias('@app/config/params.php'))) {
            return true;
        }
        return false;
    }

    /**
     * Проверяет наличие пользователей в бд.
     *
     * @return bool
     */
    public function checkUserExist(): bool
    {
        if (User::find()->count() == 0) {
            return false;
        }
        return true;
    }

    /**
     * Проверяет подключение к базе данных.
     *
     * @param Connection $connection
     * @throws Exception
     */
    public function checkDbConnection(Connection $connection): void
    {
        $connection->open();
        if ($connection === null) {
            throw new Exception('Ошибка подключения к базе данных.');
        }
    }

    /**
     * Создает конфигурационный файл базы данных.
     *
     * @param DbForm $form
     * @throws \Exception
     */
    private function createDbConfig(DbForm $form): void
    {
        $dbFile = Yii::getAlias('@app/config/db.php');
        if (!is_writable(dirname($dbFile)) || !file_put_contents($dbFile, $this->prepareDbConfigFile($form))) {
            throw new \Exception('Не удалось создать конфиг.');
        }
    }

    /**
     * Создает конфигурационный файл сайта.
     *
     * @param SettingsForm $form
     * @throws \Exception
     */
    private function createSettingsConfig(SettingsForm $form): void
    {
        $dbFile = Yii::getAlias('@app/config/params.php');
        if (!is_writable(dirname($dbFile)) || !file_put_contents($dbFile, $this->prepareSettingsConfigFile($form))) {
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
    private function migrate(): void
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
     * Удаляет установочные файлы.
     *
     * @throws ErrorException
     */
    private function deleteInstallFiles(): void
    {
        $installDir = Yii::getAlias('@app/install');
        FileHelper::removeDirectory($installDir);
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
     * Подготавливает информацию базы данных для записи в конфиг.
     *
     * @param DbForm $form
     * @return string
     */
    private function prepareDbConfigFile(DbForm $form): string
    {
        return "<?php\n\nreturn [\n   'class' => 'yii\db\Connection',\n" .
            "   'dsn' => 'mysql:host=" . $form->host . ";dbname=" . $form->dbName . "',\n" .
            "   'username' => '" . $form->username . "',\n" .
            "   'password' => '" . $form->password . "',\n" .
            "   'charset' => 'utf8',\n" .
            "   'tablePrefix' => '" . $form->tablePrefix . "',\n];\n";
    }

    /**
     * Подготавливает информацию настроек сайта для записи в конфиг.
     *
     * @param SettingsForm $form
     * @return string
     * @throws \yii\base\Exception
     */
    private function prepareSettingsConfigFile(SettingsForm $form): string
    {
        return "<?php\n\nreturn [\n" .
            "   'name' => '" . $form->name . "',\n" .
            "   'adminEmail' => '" . $form->adminEmail . "',\n" .
            "   'supportEmail' => '" . $form->supportEmail . "',\n" .
            "   'apiKey' => '" . $form->apiKey . "',\n" .
            "   'cookieValidationKey' => '" . Yii::$app->security->generateRandomString() . "',\n" .
            "];\n";
    }
}
