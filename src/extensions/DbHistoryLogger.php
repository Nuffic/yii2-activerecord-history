<?php

namespace nuffic\activerecord\history\extensions;

use yii\base\Component;
use yii\db\BaseActiveRecord;
use yii\di\Instance;
use yii\db\Connection;
use yii\helpers\Json;
use yii\db\AfterSaveEvent;
use yii\base\ModelEvent;

class DbHistoryLogger extends Component
{
    public $tableName = 'history';

    /**
     * @var Connection
     */
    public $db = 'db';

    public function init()
    {
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    public function save($event)
    {
        if (!($event->sender instanceof BaseActiveRecord)) {
            return;
        }

        $tableName = $event->sender->tableName();
        $pk = is_array($event->sender->primaryKey)?Json::encode($event->sender->primaryKey):$event->sender->primaryKey;
        $changed = date('Y-m-d H:i:s');

        $changedAttributes = [];
        if ($event instanceof AfterSaveEvent) {
            $changedAttributes=$event->changedAttributes;
        } elseif ($event instanceof ModelEvent && $event->name == BaseActiveRecord::EVENT_BEFORE_DELETE) {
            $changedAttributes=$event->sender->attributes;
        }

        $batch = array_map(function ($changedAttribute, $oldValue) use ($tableName, $pk, $changed) {
            return [$tableName, $pk, $changedAttribute, $oldValue, $changed];
        }, array_keys($changedAttributes), array_values($changedAttributes));

        $this->db->createCommand()->batchInsert($this->tableName, ['table_name', 'field_id', 'field_name', 'old_value', 'created_at'], $batch)->execute();
    }
}
