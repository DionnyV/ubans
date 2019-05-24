<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель доступа на сервере.
 *
 * @property int $user_id Пользователь
 * @property int $server_id Сервер
 * @property string $access_flags Флаги доступа
 * @property int $expire Истекает
 *
 * @property Server $server
 * @property User $user
 */
class Access extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users_servers}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'server_id', 'access_flags', 'expire'], 'required'],
            [['user_id', 'server_id', 'expire'], 'integer'],
            ['expire', 'integer', 'min' => time(), 'tooSmall' => 'Срок истечения должен быть больше текущего времени.'],
            [['access_flags'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'Пользователь'),
            'server_id' => Yii::t('app', 'Сервер'),
            'access_flags' => Yii::t('app', 'Флаги доступа'),
            'expire' => Yii::t('app', 'Истекает'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return [
            'user_id',
            'server_id',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Server::class, ['id' => 'server_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
