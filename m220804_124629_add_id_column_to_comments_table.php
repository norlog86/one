<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%comments}}`.
 */
class m220804_124629_add_id_column_to_comments_table extends Migration
{
    public function up()
    {
        $this->addColumn('comments', 'id', $this->primaryKey());
    }

    public function down()
    {
        $this->dropColumn('comments', 'id');
    }
}
