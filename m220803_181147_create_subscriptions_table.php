<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscriptions}}`.
 */
class m220803_181147_create_subscriptions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Создание таблицы subscriptions
         */
        $this->createTable('{{%subscriptions}}', [
            'user_id' => $this->integer()->null(),
            'blog_id' => $this->integer()->null(),
        ]);

        /**
         * Создание индекса для поля user_id
         */
        $this->createIndex(
            'idx-subscriptions-user_id',
            'subscriptions',
            'user_id'
        );

        /**
         * Создание индекса для поля blog_id
         */
        $this->createIndex(
            'idx-subscriptions-blog_id',
            'subscriptions',
            'blog_id'
        );

        /**
         * Создание внешнего ключа поля user_id
         */
        $this->addForeignKey(
            'fk-subscriptions-user_id',
            'subscriptions',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        /**
         * Создание внешнего ключа поля blog_id
         */
        $this->addForeignKey(
            'fk-subscriptions-blog_id',
            'subscriptions',
            'blog_id',
            'blogs',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subscriptions}}');
    }
}
