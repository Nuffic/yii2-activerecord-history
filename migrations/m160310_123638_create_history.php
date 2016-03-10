<?php

use yii\db\Migration;

class m160310_123638_create_history extends Migration
{
    public function up()
    {
        $this->createTable('history', [
            'id' => $this->primaryKey(),
            'table_name' => $this->string()->notNull(),
            'field_id' => $this->string()->notNull(),
            'field_name' => $this->string()->notNull(),
            'old_value' => $this->text(),
            'created_at' => $this->timestamp()->notNull(),
        ]);

        $this->createIndex('in_history_table_name_field_id', 'history', ['table_name', 'field_id']);
        $this->createIndex('in_history_table_name_field_name', 'history', ['table_name', 'field_name', 'field_id']);
    }

    public function down()
    {
        $this->dropIndex('in_history_table_name_field_id', 'history');
        $this->dropIndex('in_history_table_name_field_name', 'history');
        $this->dropTable('history');
    }
}
