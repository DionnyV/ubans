<?php

namespace app\models;

use app\models\query\ServerQuery;
use Yii;

/**
 * Модель сервера.
 *
 * @property int $id
 * @property int $timestamp
 * @property string $hostname
 * @property string $address
 * @property string $gametype
 * @property string $rcon
 * @property string $amxban_version
 * @property string $amxban_motd
 * @property int $motd_delay
 * @property int $amxban_menu
 * @property int $reasons
 * @property int $timezone_fixx
 *
 * @property User[] $users
 * @property Privilege[] $privileges
 */
class Server extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%servers}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp', 'motd_delay', 'amxban_menu', 'reasons', 'timezone_fixx'], 'integer'],
            [['hostname', 'address'], 'string', 'max' => 100],
            [['gametype', 'rcon', 'amxban_version'], 'string', 'max' => 32],
            [['amxban_motd'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'hostname' => Yii::t('app', 'Название'),
            'address' => Yii::t('app', 'Адрес'),
            'gametype' => Yii::t('app', 'Gametype'),
            'rcon' => Yii::t('app', 'Rcon'),
            'amxban_version' => Yii::t('app', 'Amxban Version'),
            'amxban_motd' => Yii::t('app', 'Amxban Motd'),
            'motd_delay' => Yii::t('app', 'Motd Delay'),
            'amxban_menu' => Yii::t('app', 'Amxban Menu'),
            'reasons' => Yii::t('app', 'Reasons'),
            'timezone_fixx' => Yii::t('app', 'Timezone Fixx'),
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'admin_id'])
            ->viaTable(Yii::$app->db->tablePrefix . 'users_servers', ['server_id' => 'id']);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function getPrivileges()
    {
        return $this->hasMany(Privilege::class, ['server_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ServerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServerQuery(get_called_class());
    }
}
