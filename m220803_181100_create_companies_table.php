<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%companies}}`.
 */
class m220803_181100_create_companies_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
         * Создание таблицы companies
         */
        $this->createTable('{{%companies}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'website' => $this->string(50)->notNull(),
            'address' => $this->string(50)->notNull(),
        ]);

        /**
         * Создание индекса для поля id
         */
        $this->createIndex(
            'idx-companies-id',
            'companies',
            'id'
        );

        $this->insert('companies',[
            'title' => 'test',
            'website' => 'test.ru',
            'address' => 'test',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%companies}}');
    }
}
