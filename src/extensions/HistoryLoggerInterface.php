<?php

namespace nuffic\activerecord\history\extensions;

use yii\base\Event;
/**
* 
*/
interface HistoryLoggerInterface
{
    public function save(Event $event);
}
