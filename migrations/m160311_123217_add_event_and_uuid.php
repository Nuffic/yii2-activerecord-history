<?php

use yii\db\Migration;

class m160311_123217_add_event_and_uuid extends Migration
{
    public function up()
    {
        $this->addColumn('history', 'event', $this->integer(1)->notNull());
        $this->addColumn('history', 'action_uuid', $this->string(36)->notNull());
    }

    public function down()
    {
        $this->dropColumn('history', 'event');
        $this->dropColumn('history', 'action_uuid');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
