<?php

use yii\db\Migration;

/**
 * Миграция добавляет таблицу серверов.
 */
class m190411_222845_create_servers_table extends Migration
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
        $this->createTable('{{%servers}}', [
            'id' => $this->primaryKey()->unsigned(),
            'timestamp' => $this->integer()->defaultValue(null),
            'hostname' => $this->string(100)->defaultValue('Unknown')->comment('Название'),
            'address' => $this->string(100)->defaultValue(null)->comment('IP адресс'),
            'description' => $this->text()->defaultValue(null)->comment('Описание'),
            'gametype' => $this->string(32)->defaultValue(null),
            'rcon' => $this->string(32)->defaultValue(null),
            'amxban_version' => $this->string(32)->defaultValue(null),
            'amxban_motd' => $this->string(250)->defaultValue(null),
            'motd_delay' => $this->integer()->defaultValue(10),
            'amxban_menu' => $this->integer()->notNull()->defaultValue(1),
            'reasons' => $this->integer()->defaultValue(null),
            'timezone_fixx' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addCommentOnTable('{{%servers}}', 'Сервера');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%servers}}');
    }
}
