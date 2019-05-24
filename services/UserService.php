<?php

namespace app\services;

use app\models\Access;
use app\models\form\UserForm;
use app\models\Server;
use app\models\User;
use DateTime;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Сервис пользователей.
 */
class UserService
{
    /**
     * Возвращает пользователя по идентификатору.
     *
     * @param $id
     * @return User
     * @throws Exception
     */
    public function getById($id): User
    {
        $model = User::findOne(['id' => $id, 'is_deleted' => false]);
        if ($model === null) {
            throw new Exception('Пользователь не найден.');
        }
        return $model;
    }

    /**
     * Создает пользователя.
     *
     * @param UserForm $form
     */
    public function create(UserForm $form): void
    {
        $form->save();
    }

    /**
     * Удаляет пользователя.
     *
     * @param User $user
     * @param $id
     * @throws Exception
     * @throws \Throwable
     */
    public function delete(User $user): void
    {
        if (Yii::$app->user->id == $user->id) {
            throw new Exception('Нельзя удалить текущий аккаунт.');
        }

        $user->delete();

        foreach ($user->access as $access) {
            $this->deleteAccess($access);
        }
    }

    /**
     * Возвращает права доступа пользователя.
     *
     * @param User $user
     * @param Server $server
     * @return Access
     * @throws Exception
     */
    public function findAccess(User $user, Server $server): Access
    {
        $model = Access::findOne(['user_id' => $user->id, 'server_id' => $server->id]);
        if ($model === null) {
            throw new Exception('Права доступа не найдены.');
        }
        return $model;
    }

    /**
     * Удаляет права на сервере.
     *
     * @param Access $access
     * @return false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteAccess(Access $access)
    {
        return $access->delete();
    }

    /**
     * Возвращает строку описания флагов аккаунта.
     *
     * @param User $user
     * @return string
     */
    public function formatAccountFlagsString(User $user)
    {
        $flags = ArrayHelper::merge($user->getAccountFlags(), $user->getAccountOptions());
        $value = '';
        foreach (str_split($user->flags, 1) as $item) {
            if (strlen($value) > 0) {
                $value .= ' + ';
            }
            if (isset($flags[$item])) {
                $value .= $flags[$item];
            }
        }
        if (!strpos($user->flags, 'e') && !empty($user->flags)) {
            $value .= ' + пароль';
        }
        return $value;
    }

    /**
     * Возвращает информацию об оставшемся сроке.
     *
     * @param $time
     * @return string
     * @throws Exception
     */
    public function getExpiredInfo(int $time)
    {
        if ($time === 0) {
            return 'Навсегда';
        }
        $date = new DateTime();
        $date->setTimestamp($time);
        $diff = $date->getTimestamp() - (new DateTime())->getTimestamp();
        return ($diff > 0) ? Yii::$app->formatter->asDuration($diff) : 'Истек';
    }

    /**
     * Возвращает основной флаг доступа.
     *
     * @param User $user
     * @return bool|int|string
     */
    public function getFlag(User $user)
    {
        foreach ($user->getAccountFlags() as $flag => $key) {
            if (strpos($user->flags, $flag) !== false) {
                return $flag;
            }
        }
        return false;
    }

    /**
     * Возвращает дополнительные флаги доступа.
     *
     * @param User $user
     * @return array
     */
    public function getOptions(User $user)
    {
        $selected = [];
        foreach ($user->getAccountOptions() as $flag => $key) {
            if (strpos($user->flags, $flag) !== false) {
                $selected[] = $flag;
            }
        }
        return $selected;
    }

    /**
     * Возвращает пользователя по уникальной ссылке.
     *
     * @param $code
     * @return User|null
     */
    public function findByCode($code): ?User
    {
        if (strlen(trim($code)) < 10) {
            return null;
        }
        return User::findOne(['status' => User::STATUS_ACTIVE, 'auth_key' => $code]);
    }
}
