<?php

namespace nuffic\activerecord\history\extensions;

use Ramsey\Uuid\Uuid;
use yii\base\ModelEvent;
use yii\data\ArrayDataProvider;
use yii\db\AfterSaveEvent;
use yii\db\BaseActiveRecord;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\di\Instance;
use yii\helpers\Json;

class DbHistoryLogger extends BaseHistoryLogger implements RetrievableHistoryLoggerInterface
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
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
    }

    public function save(\yii\base\Event $event)
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
            if (is_array($oldValue)) {
                $oldValue = Json::encode($oldValue);
            }
            return [$tableName, $pk, $changedAttribute, $oldValue, $changed, $actionUuid, $actionEvent];
        }, array_keys($changedAttributes), array_values($changedAttributes));

        if (count($batch)) {
            $this->db->createCommand()->batchInsert($this->tableName, ['table_name', 'field_id', 'field_name', 'old_value', 'created_at', 'action_uuid', 'event'], $batch)->execute();
        }
    }

    public function retrieve($className, $primaryKey)
    {
        $tableName = call_user_func([$className, 'tableName']);

        $primaryKey = is_array($primaryKey) ? Json::encode($primaryKey) : $primaryKey;

        if ($this->db->driverName === 'mysql') {
            $primaryKey = new Expression('BINARY :primary_key', ['primary_key' => $primaryKey]);
        }

        $current = $className::find()->where(['id' => $primaryKey])->asArray()->one();

        $query = new Query();
        $query->select(['field_name', 'old_value', 'event', 'action_uuid', 'created_at']);
        $query->from($this->tableName);
        $query->where([
            'field_id' => $primaryKey,
            'table_name' => $tableName
        ]);
        $query->orderBy(['created_at' => SORT_ASC]);

        $changes = [];

        foreach ($query->all($this->db) as $element) {
            $uuid = $element['action_uuid'];
            if (!isset($changes[$uuid])) {
                $changes[$uuid] = $current;
            }
            $changes[$uuid][$element['field_name']]=$element['old_value'];
            $current = $changes[$uuid];
        }

        $models = array_map(function ($element) use ($className){
            $model = $className::instantiate($element);
            $className::populateRecord($model, $element);
            return $model;
        }, $changes);

        return new ArrayDataProvider([
            'allModels' => array_values($models)
        ]);
    }
}
