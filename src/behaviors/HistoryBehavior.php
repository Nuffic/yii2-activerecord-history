<?php

namespace nuffic\activerecord\history\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class HistoryBehavior extends Behavior
{
    public $events = [
        BaseActiveRecord::EVENT_AFTER_UPDATE,
        BaseActiveRecord::EVENT_BEFORE_DELETE,
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

    public function saveHistory($event)
    {
        $historyComponent = Yii::$app->get('arHistory');
        $historyComponent->save($event);
    }
}
