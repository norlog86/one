<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%blogs}}`.
 */
class m220803_181127_create_blogs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%blogs}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->null(),
            'company_id' => $this->integer()->null(),
        ]);

        /**
         * Создание индекса для поля id
         */
        $this->createIndex(
            'idx-blogs-id',
            'blogs',
            'id'
        );

        /**
         * Создание индекса для поля user_id
         */
        $this->createIndex(
            'idx-blogs-user_id',
            'blogs',
            'user_id'
        );

        /**
         * Создание индекса для поля company_id
         */
        $this->createIndex(
            'idx-blogs-company_id',
            'blogs',
            'company_id'
        );

        /**
         * Создание внешнего ключа поля user_id
         */
        $this->addForeignKey(
            'fk-blogs-blog_id',
            'blogs',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        /**
         * Создание внешнего ключа поля company_id
         */
        $this->addForeignKey(
            'fk-blogs-company_id',
            'blogs',
            'company_id',
            'companies',
            'id',
            'CASCADE'
        );

        $this->insert('blogs',[
            'user_id' => '1',
        ]);
        $this->insert('blogs',[
            'user_id' => '2',
        ]);
        $this->insert('blogs',[
            'user_id' => '3',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%blogs}}');
    }
}
