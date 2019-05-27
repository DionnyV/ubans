<?php

use yii\db\Migration;

/**
 * Миграция добавляет таблицу банов.
 */
class m190313_210842_create_bans_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%bans}}', [
            'id' => $this->primaryKey()->unsigned(),
            'player_id' => $this->string(35)->defaultValue(null)->comment('Steam ID игрока'),
            'player_ip' => $this->string(32)->defaultValue(null)->comment('IP игрока'),
            'player_nick' => $this->string(100)->defaultValue('Unknown')->comment('Ник игрока'),
            'admin_id' => $this->string(35)->defaultValue('Unknown')->comment('Steam ID админа'),
            'admin_ip' => $this->string(32)->defaultValue(null)->comment('IP админа'),
            'admin_nick' => $this->string(100)->defaultValue('Unknown')->comment('Ник админа'),
            'ban_type' => $this->string(10)->defaultValue('S')->comment('Тип бана'),
            'ban_reason' => $this->string(100)->defaultValue(null)->comment('Причина'),
            'ban_created' => $this->integer()->defaultValue(null)->comment('Дата создания'),
            'ban_length' => $this->integer()->defaultValue(null)->comment('Продолжительность'),
            'server_ip' => $this->string(32)->defaultValue(null)->comment('IP сервера'),
            'server_name' => $this->string(100)->defaultValue('Unknown')->comment('Название сервера'),
            'ban_kicks' => $this->integer()->notNull()->defaultValue(0)->comment('Количество киков'),
            'expired' => $this->integer()->notNull()->defaultValue(0)->comment('Истекает'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%bans}}', 'Баны');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bans}}');
    }
}
