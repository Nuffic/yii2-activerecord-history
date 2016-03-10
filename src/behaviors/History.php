<?php

namespace nuffic\activerecord\history\behaviors;

use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class History extends Behavior
{
    public $events = [
        BaseActiveRecord::EVENT_AFTER_UPDATE,
        BaseActiveRecord::EVENT_AFTER_DELETE,
        BaseActiveRecord::EVENT_AFTER_INSERT,
    ];
}
