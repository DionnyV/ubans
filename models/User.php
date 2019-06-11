<?php

namespace app\models;

use app\models\query\UserQuery;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Модель пользователя.
 *
 * @property int $id
 * @property string $email Почта
 * @property string $username Логин
 * @property string $password_hash Пароль
 * @property string $password Пароль (только для записи)
 *
 * @property string $nickname Ник
 * @property string $player_auth Аутентификация на сервере
 * @property string $flags Доступ
 *
 * @property string $auth_key Уникальная ссылка
 * @property string $password_reset_token Токен для сброса пароля
 * @property string $verification_token Токен подтверждения прав
 * @property string $status Статус
 *
 * @property int $created_at Создан
 * @property int $updated_at Обновлен
 * @property int $is_deleted Удален
 *
 * @property Access[] $access
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const STATUS_BLOCKED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;

    public const SCENARIO_CREATE = 'create';

    /**
     * Disconnect player on invalid password.
     */
    public const ACCOUNT_FLAG_NICK = 'a';

    /**
     * Clan tag.
     */
    public const ACCOUNT_FLAG_CLAN_TAG = 'b';

    /**
     * This is steamid/wonid.
     */
    public const ACCOUNT_FLAG_STEAM_ID = 'c';

    /**
     * This is ip.
     */
    public const ACCOUNT_FLAG_IP = 'd';

    /**
     * Password is not checked (only name/ip/steamid needed).
     */
    public const ACCOUNT_FLAG_NOT_CHECKED = 'e';

    /**
     * Name or tag is case sensitive.
     */
    public const ACCOUNT_FLAG_CASE_SENSITIVE = 'k';

    private $_password;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'username', 'nickname', 'auth_key'], 'required'],
            ['password', 'required', 'on' => self::SCENARIO_CREATE],
            ['password', 'string', 'min' => 6],
            [['email', 'password', 'password_reset_token'], 'string', 'max' => 255],
            [['username', 'nickname', 'player_auth', 'flags', 'auth_key'], 'string', 'max' => 32],
            [['email', 'username', 'nickname', 'auth_key', 'password_reset_token', 'player_auth'], 'unique'],
            [['email'], 'email'],
            ['auth_key', 'default', 'value' => Yii::$app->security->generateRandomString()],
            ['auth_key', 'string', 'min' => 20],
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['flags', 'default', 'value' => self::ACCOUNT_FLAG_NICK],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_BLOCKED]],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Почта'),
            'username' => Yii::t('app', 'Логин'),
            'password' => Yii::t('app', 'Пароль'),
            'nickname' => Yii::t('app', 'Ник'),
            'player_auth' => Yii::t('app', 'Аутентификация на сервере'),
            'flags' => Yii::t('app', 'Доступ'),
            'auth_key' => Yii::t('app', 'Уникальная ссылка'),
            'status' => Yii::t('app', 'Статус'),
            'created_at' => Yii::t('app', 'Создан'),
            'updated_at' => Yii::t('app', 'Обновлен'),
            'is_deleted' => Yii::t('app', 'Удален'),
        ];
    }

    /**
     * Возвращает основные флаги аккаунта.
     * @return array
     */
    public static function getAccountFlags()
    {
        return [
            static::ACCOUNT_FLAG_NICK => 'Ник',
            static::ACCOUNT_FLAG_CLAN_TAG => 'Тег клана',
            static::ACCOUNT_FLAG_STEAM_ID => 'Steam ID',
            static::ACCOUNT_FLAG_IP => 'IP',
        ];
    }

    /**
     * Возвращает дополнительные флаги аккаунта.
     * @return array
     */
    public static function getAccountOptions()
    {
        return [
            static::ACCOUNT_FLAG_NOT_CHECKED => 'Без пароля',
            static::ACCOUNT_FLAG_CASE_SENSITIVE => 'Чувствительность к регистру символов',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccess()
    {
        return $this->hasMany(Access::class, ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Проверка пароля.
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return md5($password) === $this->password_hash;
    }

    /**
     * Создает хэш пароля и устанавливает его.
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        if (!empty($password)) {
            $this->_password = $password;
            $this->password_hash = md5($password);
        }
    }

    /**
     * Получение пароля.
     *
     * @return null
     */
    public function getPassword()
    {
        if ($this->isNewRecord) {
            return $this->_password;
        }
        return null;
    }

    /**
     * Поиск по имени пользователя.
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Поиск по токену восстановления пароля.
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Поиск по верификационному почтовому токену.
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Проверка срока действия токена восстановления пароля.
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Генерация ключа аутентификации.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Генерация токена восстановления пароля.
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Генерация верификационного почтового токена.
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Удаление токена восстановления пароля.
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Названия статусов.
     *
     * @return array
     */
    public static function getStatusLabels(): array
    {
        return [
            static::STATUS_ACTIVE => 'Активен',
            static::STATUS_BLOCKED => 'Заблокирован',
            static::STATUS_INACTIVE => 'Ожидает подтверждения',
        ];
    }

    /**
     * Блокировка изменением статуса.
     */
    public function block(): void
    {
        $this->status = static::STATUS_BLOCKED;
    }

    /**
     * {@inheritdoc}
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }
}
