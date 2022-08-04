<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%comments}}`.
 */
class m220803_191108_create_comments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%comments}}', [
            'material_id' => $this->integer()->notNull()->unique(),
            'user_id' => $this->integer()->notNull(),
            'content' => $this->text(),
        ]);

        /**
         * Создание индекса для поля user_id
         */
        $this->createIndex(
            'idx-comments-user_id',
            'comments',
            'user_id'
        );

        /**
         * Создание внешнего ключа поля user_id
         */
        $this->addForeignKey(
            'fk-comments-user_id',
            'comments',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );

        /**
         * Создание внешнего ключа поля material_id
         */
        $this->addForeignKey(
            'fk-comments-material_id',
            'comments',
            'material_id',
            'materials',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%comments}}');
    }
}
