<?php

namespace nuffic\activerecord\history\migrations;

use nuffic\activerecord\history\extensions\DbHistoryLogger;
use yii\base\InvalidConfigException;
use yii\db\Migration;
use Yii;

class m160310_123638_create_history extends Migration
{
    /**
     * @throws yii\base\InvalidConfigException
     * @return DbHistoryLogger
     */
    protected function getHistoryLogger()
    {
        $history = Yii::$app->get('arHistory');
        if (!($history instanceof DbHistoryLogger)) {
            throw new InvalidConfigException('You should configure "arHistory" component to use database before executing this migration.');
        }
        return $history;
    }

    public function up()
    {
        $this->createTable($this->historyLogger->tableName, [
            'id'          => $this->primaryKey(),
            'table_name'  => $this->string()->notNull(),
            'field_id'    => $this->string()->notNull(),
            'field_name'  => $this->string()->notNull(),
            'old_value'   => $this->text(),
            'event'       => $this->integer(1)->notNull(),
            'action_uuid' => $this->string(36)->notNull(),
            'created_at'  => $this->integer()->notNull(),
        ]);

        $this->createIndex('in_history_table_name_field_id', $this->historyLogger->tableName, ['table_name', 'field_id']);
        $this->createIndex('in_history_table_name_field_name', $this->historyLogger->tableName, ['table_name', 'field_name', 'field_id']);
    }

    public function down()
    {
        $this->dropIndex('in_history_table_name_field_id', $this->historyLogger->tableName);
        $this->dropIndex('in_history_table_name_field_name', $this->historyLogger->tableName);
        $this->dropTable($this->historyLogger->tableName);
    }
}
