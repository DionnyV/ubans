<?php

use yii\db\Migration;

/**
 * Миграция добавляет промежуточную таблицу для пользователей и серверов.
 */
class m190411_222920_create_users_servers_table extends Migration
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
        $this->createTable('{{%users_servers}}', [
            'user_id' => $this->integer()->notNull()->unsigned()->comment('Пользователь'),
            'server_id' => $this->integer()->notNull()->unsigned()->comment('Сервер'),
            'access_flags' => $this->string(32)->notNull()->comment('Флаги доступа'),
            'expire' => $this->integer()->notNull()->comment('Истекает'),
        ], $tableOptions);
        $this->createIndex('IX_users_servers', '{{%users_servers}}', ['user_id', 'server_id']);

        $this->addForeignKey(
            'FK_users_servers_user_id',
            '{{%users_servers}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK_users_servers_server_id',
            '{{%users_servers}}',
            'server_id',
            '{{%servers}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IX_users_servers', '{{%users_servers}}');
        $this->dropTable('{{%users_servers}}');
    }
}
