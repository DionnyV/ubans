<?php

namespace app\models;

use app\models\query\BanQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * Модель бана.
 *
 * @property int $id
 * @property string $player_ip
 * @property string $player_id
 * @property string $player_nick
 * @property string $admin_ip
 * @property string $admin_id
 * @property string $admin_nick
 * @property string $ban_type
 * @property string $ban_reason
 * @property int $ban_created
 * @property int $ban_length
 * @property string $server_ip
 * @property string $server_name
 * @property int $ban_kicks
 * @property int $expired
 */
class Ban extends ActiveRecord
{
    public const BANNED = 'Забанен';
    public const UNBANNED = 'Разбанен';
    public const FOREVER = 'Навсегда';
    public const EXPIRED = 'Истек';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%bans}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ban_created', 'ban_length', 'ban_kicks', 'expired'], 'integer'],
            [['player_ip', 'admin_ip', 'server_ip'], 'string', 'max' => 32],
            [['player_id', 'admin_id'], 'string', 'max' => 35],
            [['player_nick', 'admin_nick', 'ban_reason', 'server_name'], 'string', 'max' => 100],
            [['ban_type'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Id'),
            'player_ip' => Yii::t('app', 'IP игрока'),
            'player_id' => Yii::t('app', 'SteamID игрока'),
            'player_nick' => Yii::t('app', 'Ник игрока'),
            'admin_ip' => Yii::t('app', 'IP админа'),
            'admin_id' => Yii::t('app', 'SteamID админа'),
            'admin_nick' => Yii::t('app', 'Ник админа'),
            'ban_type' => Yii::t('app', 'Тип бана'),
            'ban_reason' => Yii::t('app', 'Причина'),
            'ban_created' => Yii::t('app', 'Дата'),
            'ban_length' => Yii::t('app', 'Срок'),
            'server_ip' => Yii::t('app', 'IP сервера'),
            'server_name' => Yii::t('app', 'Сервер'),
            'ban_kicks' => Yii::t('app', 'Киков'),
            'expired' => Yii::t('app', 'Истек'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return BanQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BanQuery(get_called_class());
    }
}
