<?php

use yii\db\Migration;

/**
 * Миграция добавляет таблицу пользователей.
 */
class m190411_222810_create_users_table extends Migration
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
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey()->unsigned(),
            'email' => $this->string()->notNull()->unique()->comment('Почта'),
            'username' => $this->string(32)->notNull()->unique()->comment('Логин'),
            'password' => $this->string()->notNull()->comment('Пароль'),
            'nickname' => $this->string(32)->notNull()->unique()->comment('Ник'),
            'steamid' => $this->string(32)->defaultValue(null)->comment('Steam ID'),
            'flags' => $this->string(32)->defaultValue(null)->comment('Доступ'),
            'auth_key' => $this->string(50)->notNull()->unique()->comment('Уникальная ссылка'),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('Статус'),
            'created_at' => $this->integer()->notNull()->comment('Создан'),
            'updated_at' => $this->integer()->notNull()->comment('Обновлен'),
            'is_deleted' => $this->boolean()->defaultValue(false)->comment('Удален'),
        ], $tableOptions);
        $this->addCommentOnTable('{{%users}}', 'Пользователи');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
