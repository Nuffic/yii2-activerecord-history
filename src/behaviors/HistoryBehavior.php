<?php

namespace nuffic\activerecord\history\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use nuffic\activerecord\history\extensions\RetrievableHistoryLoggerInterface;

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

    public function retrieve()
    {
        /**
         * @var \nuffic\activerecord\history\extensions\HistoryLoggerInterface
         */
        $historyComponent = $this->getComponent();;
        if (!($historyComponent instanceof RetrievableHistoryLoggerInterface)) {
            throw new \yii\base\NotSupportedException(Yii::t('arHistory', '{className} does not support retrieving history', [
                    'className' => $this->getComponent()->className()
                ]));
        }
        return $historyComponent->retrieve($this->owner->className(), $this->owner->primaryKey);
    }

    protected function getComponent()
    {
        return Yii::$app->get($this->historyComponent);
    }

    public function saveHistory($event)
    {
        $historyComponent = $this->getComponent();
        $historyComponent->save($event);
    }
}
