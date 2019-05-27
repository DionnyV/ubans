<?php

use yii\db\Migration;

/**
 * Миграция добавляет таблицу привилегий.
 */
class m190419_204456_create_privileges_table extends Migration
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
        $this->createTable('{{%privileges}}', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string()->notNull()->comment('Название'),
            'server_id' => $this->integer()->notNull()->unsigned()->comment('Сервер'),
            'access_flags' => $this->string(32)->notNull()->comment('Флаги доступа'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%privileges}}', 'Привилегии');

        $this->createIndex(
            'IX-privileges-server_id_access_flags',
            '{{%privileges}}',
            ['server_id', 'access_flags'],
            true
        );

        $this->addForeignKey(
            'FK_privileges_server_id',
            '{{%privileges}}',
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
        $this->dropIndex('IX-privileges-server_id_access_flags', '{{%privileges}}');
        $this->dropForeignKey('FK_privileges_server_id', '{{%privileges}}');
        $this->dropTable('{{%privileges}}');
    }
}
