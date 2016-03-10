<?php

namespace nuffic\activerecord\history\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class History extends Behavior
{
    public $events = [
        BaseActiveRecord::EVENT_AFTER_UPDATE,
        BaseActiveRecord::EVENT_AFTER_DELETE,
        BaseActiveRecord::EVENT_AFTER_INSERT,
    ];

    public $historyComponent = 'arHistory';

    /**
     * @inheritdoc
     */
    public function events()
    {
        $events = [];
        foreach ($this->events as $event) {
            $events[$event] = 'saveHistory';
        }
        return $events;
    }

    public function saveHistory($e)
    {
        $historyComponent = Yii::$app->get('arHistory');
        $historyComponent->save($e);
    }
}
