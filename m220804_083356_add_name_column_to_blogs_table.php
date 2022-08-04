<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%blogs}}`.
 */
class m220804_083356_add_name_column_to_blogs_table extends Migration
{
    public function up()
    {
        $this->addColumn('blogs', 'name', $this->string(50)->notNull()->defaultValue('name'));
    }

    public function down()
    {
        $this->dropColumn('blogs', 'name');
    }
}
