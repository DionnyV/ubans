<?php

namespace app\models;

use yii\base\Model;

/**
 * Справочник ролей.
 */
class RoleReference extends Model
{
    /**
     * @var string Роль по умолчанию.
     */
    public const DEFAULT_ROLE = 'user';

    /**
     * @var array Роли.
     */
    private static $roles = [
        'user' => 'Пользователь',
        'editor' => 'Редактор',
        'admin' => 'Админ',
        'deputy' => 'Заместитель',
        'root' => 'Владелец',
    ];

    /**
     * @var array Разрешения.
     */
    public static $permissions = [
        'manageBans' => 'Управление банами',
        'manageContent' => 'Управление контентом',
        'manageUsers' => 'Управление пользователями',
        'manageSettings' => 'Управление настройками',
    ];

    /**
     * Возвращает роли на проекте.
     *
     * @return array
     */
    public static function getRoles()
    {
        return self::$roles;
    }

    /**
     * Возвращает разрешения.
     *
     * @return array
     */
    public static function getPermissions()
    {
        return self::$permissions;
    }
}
