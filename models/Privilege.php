<?php

namespace app\models;

use app\models\query\PrivilegeQuery;
use Yii;

/**
 * Модель привилегии.
 *
 * @property int $id
 * @property string $name Название
 * @property int $server_id Сервер
 * @property string $access_flags Флаги доступа
 *
 * @property Server $server
 */
class Privilege extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%privileges}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'server_id', 'access_flags'], 'required'],
            [['server_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['access_flags'], 'string', 'max' => 32],
            [['server_id', 'access_flags'], 'unique', 'targetAttribute' => ['server_id', 'access_flags']],
            [
                ['server_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Server::class,
                'targetAttribute' => ['server_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Название'),
            'server_id' => Yii::t('app', 'Сервер'),
            'access_flags' => Yii::t('app', 'Флаги доступа'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServer()
    {
        return $this->hasOne(Server::class, ['id' => 'server_id']);
    }

    /**
     * {@inheritdoc}
     * @return PrivilegeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PrivilegeQuery(get_called_class());
    }
}
