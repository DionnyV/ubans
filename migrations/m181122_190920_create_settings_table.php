<?php

use yii\db\Migration;

/**
 * Миграция добавляет таблицу настроек.
 */
class m181122_190920_create_settings_table extends Migration
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

        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(11)->unsigned(),
            'key' => $this->string(64)->unique()->notNull()->comment('Ключ'),
            'name' => $this->string(128)->notNull()->comment('Наименование'),
            'value' => $this->string(256)->null()->comment('Значение'),
            'type' => $this->integer(3)->unsigned()->notNull()->comment('Тип'),
            'created_at' => $this->integer()->notNull()->comment('Дата создания'),
            'updated_at' => $this->integer()->notNull()->comment('Дата обновления'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%settings}}', 'Настройки');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
