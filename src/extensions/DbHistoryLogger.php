<?php

namespace nuffic\activerecord\history\extensions;

use Ramsey\Uuid\Uuid;
use yii\base\Component;
use yii\db\BaseActiveRecord;
use yii\di\Instance;
use yii\db\Connection;
use yii\helpers\Json;
use yii\db\AfterSaveEvent;
use yii\base\ModelEvent;

class DbHistoryLogger extends Component
{
    const EVENT_UPDATE = 0;
    const EVENT_DELETE = 1;

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
        $actionUuid = Uuid::uuid4()->toString();

        $changedAttributes = [];
        $actionEvent = self::EVENT_UPDATE;
        if ($event instanceof AfterSaveEvent) {
            $changedAttributes=$event->changedAttributes;
        } elseif ($event instanceof ModelEvent && $event->name == BaseActiveRecord::EVENT_BEFORE_DELETE) {
            $changedAttributes=$event->sender->attributes;
            $actionEvent = self::EVENT_DELETE;
        }

        $batch = array_map(function ($changedAttribute, $oldValue) use ($tableName, $pk, $changed, $actionUuid, $actionEvent) {
            return [$tableName, $pk, $changedAttribute, $oldValue, $changed, $actionUuid, $actionEvent];
        }, array_keys($changedAttributes), array_values($changedAttributes));

        if (count($batch)) {
            $this->db->createCommand()->batchInsert($this->tableName, ['table_name', 'field_id', 'field_name', 'old_value', 'created_at', 'action_uuid', 'event'], $batch)->execute();
        }
    }
}
