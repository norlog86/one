<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m220803_180911_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Создание таблицы users
         */
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'login' => $this->string(15)->notNull()->unique(),
            'avatar' => $this->string()->null(),
            'email' => $this->string(50)->notNull()->unique(),
            'phone' => $this->integer()->notNull()->unique(),
            'website' => $this->string(50)->null(),
        ]);

        /**
         * Создание индекса для поля id
         */
        $this->createIndex(
            'idx-users-id',
            'users',
            'id'
        );


        $this->insert('users',[
            'name' => 'Ivan',
            'login' => 'Ivan',
            'email' => 'Ivan@mail.ru',
            'phone' => '231',
        ]);

        $this->insert('users',[
            'name' => 'Petr',
            'login' => 'Petr',
            'email' => 'Petr@mail.ru',
            'phone' => '123',
        ]);

        $this->insert('users',[
            'name' => 'Fedya',
            'login' => 'Fedya',
            'email' => 'Fedya@mail.ru',
            'phone' => '312',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
